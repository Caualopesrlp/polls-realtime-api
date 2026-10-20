<?php

namespace App\Enums;

enum PollStatus: string
{
    case NAO_INICIADO = 'nao_iniciado';
    case INICIADO = 'iniciado';
    case EM_ANDAMENTO = 'em_andamento';
    case FINALIZADO = 'finalizado';
}
