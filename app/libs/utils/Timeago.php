<?php

namespace app\libs\utils;


class Timeago
{

    public static function convert($ptime)
    {
        $time_ago = strtotime($ptime);
        $cur_time   = time();
        $time_elapsed   = $cur_time - $time_ago;
        $seconds    = $time_elapsed ;
        $minutes    = round($time_elapsed / 60 );
        $hours      = round($time_elapsed / 3600);
        $days       = round($time_elapsed / 86400 );
        $weeks      = round($time_elapsed / 604800);
        $months     = round($time_elapsed / 2600640 );
        $years      = round($time_elapsed / 31207680 );
        if($seconds <= 60){
            return "Agora mesmo";
        }
        else if($minutes <=60){
            if($minutes==1){
                return "um minuto atrás";
            }
            else{
                return "$minutes minutos atrás";
            }
        }
        else if($hours <=24){
            if($hours==1){
                return "uma hora atrás";
            }else{
                return "$hours horas atrás";
            }
        }
        else if($days <= 7){
            if($days==1){
                return "Ontem";
            }else{
                return "$days dias atrás";
            }
        }
        else if($weeks <= 4.3){
            if($weeks==1){
                return "uma semana";
            }else{
                return "$weeks semanas atrás";
            }
        }
        else if($months <=12){
            if($months==1){
                return "um mês atrás";
            }else{
                return "$months meses atrás";
            }
        }
        else{
            if($years==1){
                return "um ano atrás";
            }else{
                return "$years anos atrás";
            }
        }
    }

}