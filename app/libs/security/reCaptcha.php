<?php

namespace app\libs\security;

class reCaptcha
{

    private $secretKey = "";

    public function __construct($secretKey = null)
    {
        if($secretKey != null)
            $this->secretKey = $secretKey;
    }

    public function check($response)
    {
        $post_data = http_build_query([
            'secret' => $this->secretKey,
            'response' => $response
        ]);

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            ]
        ];

        $context  = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);

        return $result->success;
    }

}