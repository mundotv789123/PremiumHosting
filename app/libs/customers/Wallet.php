<?php

namespace app\libs\customers;

use app\system\DB;

class Wallet
{

    private $db;
    private $wallet;

    public function __construct()
    {
        $this->db = new DB();
        $this->wallet = Profile::wallet();
    }

    public function get($customerId)
    {
        return $this->db->select('SELECT * FROM `customers` WHERE `id`=?', [$customerId])[0]->wallet;
    }

    public function add($amount, $customerId)
    {
        $wallet = $this->wallet + $amount;

        $this->db->update([
            "wallet" => $wallet
        ], [ "id" => $customerId ], "customers");
    }

    public function remove($amount, $customerId = null)
    {
        $wallet = $this->wallet - $amount;

        $this->db->update([
            "wallet" => $wallet
        ], [ "id" => $customerId ], "customers");
    }

}