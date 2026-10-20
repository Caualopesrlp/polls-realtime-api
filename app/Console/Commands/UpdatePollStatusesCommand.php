<?php

namespace App\Console\Commands;

use App\Enums\PollStatus;
use App\Models\Poll;
use Illuminate\Console\Command;

class UpdatePollStatusesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polls:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o status automaticamente baseado nas datas de início e fim';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Poll::where('status', PollStatus::NAO_INICIADO)->where('start_date', '<=', now())->update(['status' => PollStatus::EM_ANDAMENTO]);
        Poll::where('status', '!=', PollStatus::FINALIZADO)->where('end_date', '<=', now())->update(['status' => PollStatus::FINALIZADO]);
        $this->info('Status das enquetes atualizados com sucesso!');
    }
}
