<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePollRequest;
use App\Http\Requests\UpdatePollRequest;
use App\Services\PollService;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

class PollController extends Controller
{
    private PollService $service;

    public function __construct(PollService $service)
    {
        $this->service = $service;
    }
    public function storePoll(StorePollRequest $request)
    {

        $dados = $request->validated();

        $response = $this->service->storePoll($dados);

        return response()->json($response, 201);
    }

    public function index()
    {

        $response = $this->service->index();

        return response()->json($response, 200);
    }

    public function update(UpdatePollRequest $request, string $uuid)
    {

        $dados = $request->validated();

        $response = $this->service->update($dados, $uuid);

        return response()->json($response, 200);
    }

    public function vote(string $pollId, string $optionId)
    {

        $response = $this->service->vote($pollId, $optionId);

        return response()->json($response, 200);
    }

    public function show(string $pollId)
    {
        $response = $this->service->show($pollId);

        return response()->json($response, 200);
    }
}
