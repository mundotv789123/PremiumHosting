<?php

namespace app\libs\utils;

class Proxy
{

    public static function get()
    {
        $proxies = file_get_contents("./cdn/proxy.txt");
        $proxies = explode("\n", $proxies);

        shuffle($proxies);

        foreach ($proxies as $proxy) {

            if(empty($proxy))
                continue;

            $handle = self::sendResquest(trim($proxy));

            if($handle) {
                return $proxy;
            }
        }

        return false;
    }

    public static function check($proxy)
    {
        $handle = self::sendResquest(trim($proxy));

        if($handle) {
            return true;
        }

        return false;
    }

    public static function getFree()
    {
        $result = file_get_contents('https://api.getproxylist.com/proxy?anonymity[]=high%20anonymity&protocol=http&allowsPost&allowsHttps');
        $result = json_decode($result);

        return "{$result->ip}:{$result->port}";
    }

    private static function sendResquest($proxy)
    {
        $ch = curl_init("http://google.com");
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        $handle = curl_exec($ch);
        curl_close($ch);
        return $handle;
    }

}