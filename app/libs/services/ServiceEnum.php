<?php

namespace app\libs\services;

class ServiceEnum
{

    const PENDING = 'PENDING';
    const ACTIVE = 'ACTIVE';
    const SUSPENSE = 'SUSPENDED';
    const REMOVED = 'REMOVED';

    public static function translate($status)
    {
        switch ($status) {
            case 'PENDING':
                return 'Pendente';
                break;
            case 'ACTIVE':
                return 'Ativo';
                break;
            case 'SUSPENDED':
                return 'Suspenso';
                break;
            case 'REMOVED':
                return 'Cancelado';
                break;
            default:
                return 'Sem status';
                break;
        }
    }

}