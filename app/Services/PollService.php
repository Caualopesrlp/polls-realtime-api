<?php

namespace App\Services;

use App\Repositories\PollRepository;
use Illuminate\Support\Facades\DB;
use Nette\Schema\ValidationException;

class PollService
{

    private PollRepository $repository;

    public function __construct(PollRepository $repository)
    {
        $this->repository = $repository;
    }

    public function storePoll(array $dados)
    {

        return DB::transaction(function () use ($dados) {
            return $this->repository->storePoll($dados);
        });
    }

    public function index()
    {
        return $this->repository->index();
    }

    public function update(array $dados, string $uuid)
    {

        return DB::transaction(function () use ($dados, $uuid) {
            $update = $this->repository->update($dados, $uuid);

            if ($update == false) {
                abort(422, 'A enquete já foi iniciada e não pode ser editada.');
            }

            return $update;
        });
    }

    public function vote(string $pollId, string $optionId)
    {
        return DB::transaction(function () use ($pollId, $optionId){
            $voto = $this->repository->vote($pollId, $optionId);

            if($voto === 'POLL_CLOSED'){
                abort(422, 'A enquete não pode receber votos.');
            } else if ($voto === 'INVALID_OPTION'){
                abort(422, 'Não é possível votar nessa opção');
            }

            return $voto;
        });
    }
}
