<?php

namespace app\libs\security;

class CSRF
{

    public function getTokenID()
    {
        if(isset($_SESSION['token_id']))
        {
            return $_SESSION['token_id'];
        }

        $token_id = $this->random();
        $_SESSION['token_id'] = $token_id;

        return $token_id;
    }

    public function getToken()
    {
        if(isset($_SESSION['token_value']))
        {
            return $_SESSION['token_value'];
        }

        $token = hash('sha256', $this->random(256));
        $_SESSION['token_value'] = $token;

        return $token;
    }

    public function check($method) {
        if(isset($method[$this->getTokenID()]) && ($method[$this->getTokenID()] == $this->getToken())) {
            return true;
        }
        return false;
    }

    public function inputs($names, $regenerate) {

        $values = [];

        foreach ($names as $n) {

            if($regenerate == true) {
                unset($_SESSION['i-'.$n]);
            }

            $s = isset($_SESSION['i-'.$n]) ? $_SESSION['i-'.$n] : $this->random();

            $_SESSION['i-'.$n] = $s;
            $values['i-'.$n] = $this->sqlProtect($s);
        }

        return $values;
    }

    public function clear()
    {
        unset($_SESSION['token_id']);
        unset($_SESSION['token_value']);

        foreach ($_SESSION as $key => $value)
        {
            if(strpos($key, 'i-') !== false)
                unset($_SESSION[$key]);
        }
    }

    public function getInputs()
    {
        $inputs = [];

        foreach ($_SESSION as $key => $value)
        {
            if(strpos($key, 'i-') !== false)
                $inputs[$key] = $value;
        }

        return $inputs;
    }

    private function random($size = 10)
    {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';

        $callback = '';

        $words = '';
        $words .= $lmin;
        $words .= $lmai;
        $words .= $num;
        $words .= $simb;

        $len = strlen($words);
        for ($n = 1; $n <= $size ; $n++) {

            $rand = mt_rand(1, $len);
            $callback .= $words[$rand-1];

        }

        return $callback;
    }

    private function sqlProtect($str)
    {
        return addslashes(strip_tags($str));
    }

}