<?php

namespace App\Repositories;

use App\Models\Option;
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

    public function update(array $dados, string $uuid)
    {
        $options = $dados['options'];
        unset($dados['options']);

        $poll = Poll::findOrFail($uuid);

        if ($poll->isStarted()) {
            return false;
        }

        $poll->update($dados);
        $poll->options()->delete();
        $poll->options()->createMany($options);

        return $poll->load('options');
    }

    public function vote(string $pollId, string $optionId)
    {
        $poll = Poll::findOrFail($pollId);

        if (!$poll->isOpen()) {
            return 'POLL_CLOSED';
        }

        $option = Option::where('id', $optionId)
            ->where('poll_id', $pollId)
            ->increment('votes');

        if ($option === 0) {
            return 'INVALID_OPTION';
        }

        $totalVotos = Option::where('id', $optionId)
            ->where('poll_id', $pollId)
            ->value('votes');

        return $totalVotos;
    }

    public function show(string $pollId)
    {
        return Poll::with('options')->findOrFail($pollId);
    }

    public function totalVotos(string $pollId)
    {
        return Option::where('poll_id', $pollId)->sum('votes');
    }
}
