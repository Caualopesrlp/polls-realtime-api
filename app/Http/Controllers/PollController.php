<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePollRequest;
use App\Http\Requests\UpdatePollRequest;
use App\Http\Resources\PollResource;
use App\Services\PollService;

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

        return (new PollResource($response))->response()->setStatusCode(201);
    }

    public function index()
    {
        return PollResource::collection($this->service->index());
    }

    public function update(UpdatePollRequest $request, string $uuid)
    {
        $dados = $request->validated();
        $response = $this->service->update($dados, $uuid);

        return new PollResource($response);
    }

    public function vote(string $pollId, string $optionId)
    {
        $response = $this->service->vote($pollId, $optionId);

        return response()->json([
            'message' => 'Voto computado com sucesso!',
            'total_votos' => $response,
        ], 200);
    }

    public function show(string $pollId)
    {
        return new PollResource($this->service->show($pollId));
    }
}
