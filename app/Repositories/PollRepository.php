<?php

namespace App\Repositories;

use App\Models\Poll;

class PollRepository
{

    public function storePoll(array $dados)
    {
        $options = $dados['options'];
        unset($dados['options']);

        $poll = Poll::create($dados);
        $poll->options()->createMany($options);
        $poll->load('options');

        return $poll;
    }

    public function index()
    {

        return Poll::with('options')->get();
    }
}
