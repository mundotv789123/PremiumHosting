<?php

namespace app\libs\security;

use app\system\Config;

class Ajax
{

    public static function check()
    {
        return strpos($_SERVER['HTTP_REFERER'], Config::DOMAIN);
    }

}