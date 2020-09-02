<?php

namespace app\libs\utils;

class Geocoder
{

    public static function data($ip)
    {
        $response = file_get_contents("http://ip-api.com/json/{$ip}");
        return json_decode($response);
    }

}