<?php

namespace app\libs\services;

class PlanEnum
{

    public static function translate($status)
    {
        switch ($status) {
            case 'MINECRAFT_SINGLE':
                return 'Minecraft Single Server';
                break;
            case 'MINECRAFT_BUNGEECORD':
                return 'Minecraft BungeeCord';
                break;
            case 'CLOUD_COMPUTING':
                return 'Cloud Computing';
                break;
            default:
                return 'Desconhecido';
                break;
        }
    }

}