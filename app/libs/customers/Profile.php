<?php

namespace app\libs\customers;


use app\system\DB;

class Profile
{

    const CUSTOMER_ID = 'HyMC@CustomerId';

    public static function username($id = null)
    {
        $db = new DB();
        if($id) {
            return $db->select("SELECT * FROM `customers` WHERE `id`=?", [$id])[0]->name;
        }

        return $db->select("SELECT * FROM `customers` WHERE `id`=?", [Authentication::id()])[0]->name;
    }

    public static function email($id = null)
    {
        $db = new DB();
        if($id) {
            return $db->select("SELECT * FROM `customers` WHERE `id`=?", [$id])[0]->email;
        }

        return $db->select("SELECT * FROM `customers` WHERE `id`=?", [Authentication::id()])[0]->email;
    }

    public static function wallet()
    {
        $db = new DB();
        return $db->select("SELECT * FROM `customers` WHERE `id`=?", [Authentication::id()])[0]->wallet;
    }

    public static function services($status = null)
    {
        $db = new DB();

        if($status) {
            return $db->select("SELECT * FROM `services` WHERE `customer_id`=? AND `status`=?", [Authentication::id(), $status]);
        }

        return $db->select("SELECT * FROM `services` WHERE `customer_id`=?", [Authentication::id()]);
    }

    public static function isAdmin()
    {
        $db = new DB();
        return $db->select("SELECT * FROM `customers` WHERE `id`=?", [Authentication::id()])[0]->admin;
    }

    public static function hasEmail($email)
    {
        $db = new DB();
        return $db->select("SELECT * FROM `customers` WHERE `email`=?", [$email]);
    }

    public static function hasUsername($username)
    {
        $db = new DB();
        return $db->select("SELECT * FROM `customers` WHERE `username`=?", [$username]);
    }

    public static function tickets()
    {
        $db = new DB();
        return $db->select("SELECT * FROM `tickets` WHERE `owner`=? ORDER BY `id` DESC", [Authentication::id()]);
    }

    public static function transactions()
    {
        $db = new DB();
        return $db->select("SELECT * FROM `transactions` WHERE `customer`=? ORDER BY `id` DESC", [Authentication::id()]);
    }

}