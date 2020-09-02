<?php

namespace app\libs\customers;

use app\system\DB;

class Customer
{

    private $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public static function getUsername($customerId)
    {
        $db = new DB();
        $data =  $db->select("SELECT * FROM `customers` WHERE `id`=?", [$customerId])[0];

        return "{$data->name} {$data->surname}";
    }

    public function data()
    {
        return $this->db->select("SELECT * FROM `customers`");
    }

}