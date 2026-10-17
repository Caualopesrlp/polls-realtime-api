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
}
