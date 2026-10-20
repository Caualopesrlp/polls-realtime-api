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

        return $poll->load('options');
    }

    public function index()
    {
        return Poll::with('options')->get();
    }

    public function update(array $dados, string $uuid)
    {
        $poll = Poll::findOrFail($uuid);

        if ($poll->isStarted()) {
            return false;
        }

        if (isset($dados['options'])) {
            $options = $dados['options'];
            unset($dados['options']);
            $poll->options()->delete();
            $poll->options()->createMany($options);
        }

        $poll->update($dados);

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

        return Option::where('id', $optionId)
            ->where('poll_id', $pollId)
            ->value('votes');
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
