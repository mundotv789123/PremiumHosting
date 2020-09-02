<?php

namespace app\libs\utils;

class Curl
{

    private $uri;

    private $body;

    private $headers = [];

    public function __construct($uri, $payload)
    {
        $this->uri = $uri;
        $this->body = $payload;
    }

    public function setHeaders($headers = [])
    {
        $this->headers = $headers;
    }

    public function post()
    {
        $curl = curl_init($this->uri);

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_PROXY, "proxy.apify.com:8000");
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, "auto:Rz4XQrYKyLpxZ47dHh6rP7zqS");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl,CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->body));

        $json_response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($json_response);

        return $response;
    }

}