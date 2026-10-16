<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePollRequest;
use App\Services\PollService;
use Illuminate\Http\Request;

class PollController extends Controller
{
    private PollService $service;

    public function __construct(PollService $service)
    {
        $this->service = $service;
    }
    public function storePoll(StorePollRequest $request){

        $dados = $request->validated();

        $response = $this->service->storePoll($dados);

        return response()->json($response, 201);
    }
}
