<?php

namespace app\libs\security;

class XSS
{

    public static function filter($string)
    {
        return htmlspecialchars($string);
    }

}