<?php

namespace app\libs\utils;

class QueryParser
{

    public static function decode($str)
    {
        $data = explode('&', $str);
        $arry = [];

        foreach ($data as $dt) {
            $info = explode('=', $dt);
            $arry[$info[0]] = $info[1];
        }

        return $arry;
    }

}