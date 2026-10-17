<?php

namespace App\Services;

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

        return DB::transaction(function () use ($dados) {
            return $this->repository->storePoll($dados);
        });
    }

    public function index()
    {
        return $this->repository->getPoll();
    }
}
