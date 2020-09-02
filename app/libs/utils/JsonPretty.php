<?php

namespace app\libs\utils;

class JsonPretty
{

    public static function encode($arry)
    {
        header('Content-Type: json/application');

        return json_encode($arry, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }

}