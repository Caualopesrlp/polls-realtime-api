<?php

use App\Http\Controllers\PollController;
use Illuminate\Support\Facades\Route;

Route::post('/polls', [PollController::class, 'storePoll']);

Route::get('/polls', [PollController::class, 'index']);

Route::put('/polls/{uuid}', [PollController::class, 'update']);

Route::post('/polls/{id}/votes/{optionId}', [PollController::class, 'vote']);
