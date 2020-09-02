<?php

namespace app\libs\financial;

use app\system\DB;

class Transaction
{

    private $db;

    public function __construct()
    {
        $this->db = new DB();
        $this->__table();
    }

    public function store($gateway, $code, $customer, $name, $email, $gross, $amount, $reference, $status, $paid)
    {
        $select = $this->db->select("SELECT * FROM `transactions` WHERE `code`=?", [$code]);

        if(!$select) {
            $this->db->insert([
                'code' => $code,
                'gateway' => $gateway,
                'customer' => $customer,
                'payer_name' => $name,
                'payer_email' => $email,
                'gross_amount' => $gross,
                'net_amount' => $amount,
                'reference' => $reference,
                'status' => $status,
                'date' => date('Y-m-d H:i:s'),
                'paid' => $paid
            ], 'transactions');
        }else{
            $this->db->update([
                'name' => $name,
                'email' => $email,
                'status' => $status,
                'paid' => $paid
            ], [ 'code' => $code ], 'transactions');
        }
    }

    public function hasPaid($code)
    {
        return $this->db->select("SELECT * FROM `transactions` WHERE `code`=?", [$code])[0]->paid;
    }

    public function monthSales()
    {
        $month = date("Y-m");
        return $this->db->select("SELECT COUNT(*) as total FROM `transactions` WHERE `date` LIKE '%{$month}' AND `paid`=?", [true])[0]->total;
    }

    public function monthEarn()
    {
        $month = date("Y-m");
        return $this->db->select("SELECT SUM(`net_amount`) as soma FROM `transactions` WHERE `date` LIKE '%{$month}' AND `paid`=?", [true])[0]->soma;
    }

    public function getEarnInDate($date)
    {
        $date = explode("/", $date);
        $converted = "{$date[2]}-{$date[1]}-{$date[0]}";
        return $this->db->select("SELECT SUM(`net_amount`) as soma FROM `transactions` WHERE `date` LIKE '%{$converted}' AND `paid`=?", [true])[0]->soma;
    }

    public function data($data = [])
    {
        $query = "";

        if(isset($data['limit'])) {
            $query .= " LIMIT {$data['limit']}";
        }

        return $this->db->select("SELECT * FROM `transactions` {$query}");
    }

    private function __table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `transactions` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `code` VARCHAR(255) NOT NULL , `reference` VARCHAR(60) NOT NULL , `customer` VARCHAR(60) NOT NULL , `payer_name` VARCHAR(125) NOT NULL , `payer_email` VARCHAR(125) NOT NULL , `gross_amount` DECIMAL(10,2) NOT NULL , `net_amount` DECIMAL(10,2) NOT NULL , `status` VARCHAR(26) NOT NULL , `date` DATETIME NOT NULL , `gateway` VARCHAR(30) NOT NULL DEFAULT 'false' , `paid` BOOLEAN NOT NULL DEFAULT FALSE , PRIMARY KEY (`id`)) ENGINE = MyISAM;";
        $this->db->query($sql);
    }

}