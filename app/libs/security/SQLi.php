<?php

namespace app\libs\security;

class SQLi
{

    public static function filter_string($string)
    {
        return preg_replace('/[^[:alpha:]_]/', '', $string);
    }

    public static function filter_password($password)
    {
        return preg_replace('/[^[:alnum:]_]/', '', $password);
    }

}