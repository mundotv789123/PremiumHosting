<?php

namespace app\libs\gateways;

use app\system\Config;

class PayPal
{

    private $email = "";
    private $items = [];
    private $custom = "";
    private $first_name;
    private $last_name;
    private $customer_email;
    private $return_url = "";
    private $cancel_url = "";
    private $notify_url = "";
    private $location = 'BR';
    private $currency = 'BRL';
    private $sandbox;


    public function __construct() {
        date_default_timezone_set('America/Sao_Paulo');
        $this->sandbox = Config::SANDBOX;
    }

    public function setCredential($email)
    {
        $this->email = $email;
    }

    public function setSandbox()
    {
        $this->sandbox = true;
    }

    public function setReference($reference)
    {
        $this->custom = $reference;
    }

    public function setNotificationURL($url)
    {
        $this->notify_url = $url;
    }

    public function setLocation($lc)
    {
        $this->location = $lc;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function setReturnURL($url)
    {
        $this->return_url = $url;
    }

    public function setCancelURL($url)
    {
        $this->cancel_url = $url;
    }

    public function setName($first, $last)
    {
        $this->first_name = $first;
        $this->last_name = $last;
    }

    public function setCustomerEmail($email)
    {
        $this->customer_email = $email;
    }

    public function addItem($name, $amount, $quantity)
    {
        $this->items[] = [
            'name' => $name,
            'amount' => number_format($amount, 2, '.', ''),
            'quantity' => (int) $quantity
        ];
    }

    public function checkout()
    {

        $query = [];

        $query['cmd'] = '_cart';
        $query['upload'] = 1;
        $query['business'] = $this->getCredential();

        foreach ($this->getItems() as $id => $item)
        {
            $id = $id + 1;

            $query['item_name_' . $id] = $item['name'];
            $query['amount_' . $id] = $item['amount'];
            $query['quantity_' . $id] = $item['quantity'];
        }

        $query['custom'] = $this->getReference();

        $query['<first_name>'] = $this->first_name;
        $query['<last_name>'] = $this->last_name;
        $query['<email>'] = $this->customer_email;

        $query['notify_url'] = $this->getNotificationURL();
        $query['return'] = $this->getReturnURL();
        $query['cancel_return'] = $this->getCancelURL();

        $query['rm'] = '2';
        $query['cbt'] = 'Retornar para o site';
        $query['lc'] = $this->getLocation();
        $query['currency_code'] = $this->getCurrency();

        $query_string = http_build_query($query);

        return "https://". ($this->isSandbox() ? 'sandbox' : 'www' ) .".paypal.com/cgi-bin/webscr?".$query_string;
    }

    public function isIPNValid($post)
    {
        $endpoint = 'https://www.paypal.com';

        $endpoint .= '/cgi-bin/webscr?cmd=_notify-validate';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $errno = curl_errno($curl);

        curl_close($curl);


        return empty($error) && $errno == 0 && $response == 'VERIFIED';
    }

    private function getCredential()
    {
        return $this->email;
    }

    private function getNotificationURL()
    {
        return $this->notify_url;
    }

    private function getReference()
    {
        return $this->custom;
    }

    private function getLocation()
    {
        return $this->location;
    }

    private function getCurrency()
    {
        return $this->currency;
    }

    private function getReturnURL()
    {
        return $this->return_url;
    }

    private function getCancelURL()
    {
        return $this->cancel_url;
    }

    private function getItems()
    {
        return $this->items;
    }

    private function isSandbox()
    {
        return $this->sandbox;
    }

}