<?php

namespace Tests\Feature;

use App\Enums\PollStatus;
use App\Events\OptionVoted;
use App\Models\Poll;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class PollControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_criar_enquete_com_sucesso(): void
    {
        $payload = [
            'question' => 'Qual o seu framework favorito?',
            'start_date' => now()->addDay()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'options' => [
                ['text' => 'Laravel'],
                ['text' => 'Spring Boot'],
                ['text' => 'Express.js'],
            ],
        ];

        $response = $this->postJson('/api/polls', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'question',
                'status',
                'start_date',
                'end_date',
                'options' => [
                    '*' => ['id', 'poll_id', 'text', 'votes'],
                ],
            ]);

        $this->assertDatabaseHas('polls', [
            'question' => 'Qual o seu framework favorito?',
        ]);

        $this->assertDatabaseCount('options', 3);
    }

    public function test_nao_deve_criar_enquete_com_menos_de_tres_opcoes(): void
    {
        $payload = [
            'question' => 'Qual o seu framework favorito?',
            'start_date' => now()->addDay()->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'options' => [
                ['text' => 'laravel'],
                ['text' => 'spring'],
            ],
        ];

        $response = $this->postJson('/api/polls', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['options']);

        $this->assertDatabaseMissing('polls', [
            'question' => 'Qual o seu framework favorito?',
        ]);

        $this->assertDatabaseCount('options', 0);
    }

    public function test_deve_retornar_total_de_votos_e_porcentagem_no_resource(): void
    {
        $poll = Poll::create([
            'question' => 'Qual o seu framework favorito?',
            'status' => PollStatus::INICIADO,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(7),
        ]);

        $poll->options()->create(['text' => 'Laravel', 'votes' => 3]);
        $poll->options()->create(['text' => 'Spring Boot', 'votes' => 1]);
        $poll->options()->create(['text' => 'Express.js', 'votes' => 0]);

        $response = $this->getJson("/api/polls/{$poll->id}");

        $response->assertStatus(200)
            ->assertJsonPath('total_votes', 4)
            ->assertJsonPath('options.0.percentage', 75)
            ->assertJsonPath('options.1.percentage', 25)
            ->assertJsonPath('options.2.percentage', 0);
    }

    public function test_pode_editar_enquete_nao_iniciada(): void
    {
        $poll = Poll::create([
            'question' => 'Qual o seu framework favorito?',
            'status' => PollStatus::NAO_INICIADO,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        $poll->options()->create(['text' => 'Laravel']);
        $poll->options()->create(['text' => 'Spring Boot']);
        $poll->options()->create(['text' => 'Express.js']);

        $payload = [
            'question' => 'Qual é o seu framework favorito?',
        ];

        $response = $this->putJson("/api/polls/{$poll->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('polls', [
            'id' => $poll->id,
            'question' => 'Qual é o seu framework favorito?',
        ]);
    }

    public function test_nao_deve_permitir_editar_enquete_ja_iniciada(): void
    {
        $poll = Poll::create([
            'question' => 'Qual o seu framework favorito?',
            'status' => PollStatus::INICIADO,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(7),
        ]);

        $poll->options()->create(['text' => 'Laravel']);
        $poll->options()->create(['text' => 'Spring Boot']);
        $poll->options()->create(['text' => 'Express.js']);

        $payload = [
            'question' => 'Qual é o seu framework favorito?',
        ];

        $response = $this->putJson("/api/polls/{$poll->id}", $payload);

        $response->assertStatus(422);
        $this->assertDatabaseHas('polls', [
            'id' => $poll->id,
            'question' => 'Qual o seu framework favorito?',
        ]);
    }

    public function test_deve_computar_voto_com_sucesso_e_disparar_evento(): void
    {
        Event::fake();

        $poll = Poll::create([
            'question' => 'Qual o seu framework favorito?',
            'status' => PollStatus::INICIADO,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(7),
        ]);

        $option = $poll->options()->create(['text' => 'Laravel', 'votes' => 0]);

        $response = $this->postJson("/api/polls/{$poll->id}/vote/{$option->id}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('options', [
            'id' => $option->id,
            'votes' => 1,
        ]);

        Event::assertDispatched(OptionVoted::class, function (OptionVoted $event) use ($poll, $option) {
            return $event->pollId === $poll->id
                && $event->optionId === $option->id
                && $event->votes === 1
                && $event->totalVotes === 1
                && $event->percentage === 100.0;
        });
    }

    public function test_nao_deve_permitir_votar_em_enquete_fechada(): void
    {
        $poll = Poll::create([
            'question' => 'Qual o seu framework favorito?',
            'status' => PollStatus::FINALIZADO,
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(2),
        ]);

        $option = $poll->options()->create(['text' => 'Laravel', 'votes' => 0]);

        $response = $this->postJson("/api/polls/{$poll->id}/vote/{$option->id}");

        $response->assertStatus(422);

        $this->assertDatabaseHas('options', [
            'id' => $option->id,
            'votes' => 0,
        ]);
    }

    public function test_nao_deve_permitir_votar_em_opcao_inexistente(): void
    {
        $poll = Poll::create([
            'question' => 'Qual o seu framework favorito?',
            'status' => PollStatus::INICIADO,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(7),
        ]);

        $fakeOptionId = (string) Str::uuid();

        $response = $this->postJson("/api/polls/{$poll->id}/vote/{$fakeOptionId}");

        $response->assertStatus(422);
    }
}
