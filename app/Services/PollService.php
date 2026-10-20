<?php

namespace App\Services;

use App\Events\OptionVoted;
use App\Repositories\PollRepository;
use Illuminate\Support\Facades\DB;

class PollService
{
    private PollRepository $repository;

    public function __construct(PollRepository $repository)
    {
        $this->repository = $repository;
    }

    public function storePoll(array $dados)
    {
        return DB::transaction(fn() => $this->repository->storePoll($dados));
    }

    public function index()
    {
        return $this->repository->index();
    }

    public function update(array $dados, string $uuid)
    {
        return DB::transaction(function () use ($dados, $uuid) {
            $update = $this->repository->update($dados, $uuid);

            if ($update === false) {
                abort(422, 'A enquete já foi iniciada e não pode ser editada.');
            }

            return $update;
        });
    }

    public function vote(string $pollId, string $optionId)
    {
        return DB::transaction(function () use ($pollId, $optionId) {
            $voto = $this->repository->vote($pollId, $optionId);

            if ($voto === 'POLL_CLOSED') {
                abort(422, 'A enquete não pode receber votos.');
            }

            if ($voto === 'INVALID_OPTION') {
                abort(422, 'Não é possível votar nessa opção');
            }

            OptionVoted::dispatch($pollId, $optionId, $voto);

            return $voto;
        });
    }

    public function show(string $pollId)
    {
        return $this->repository->show($pollId);
    }
}
