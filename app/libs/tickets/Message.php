<?php

namespace app\libs\tickets;

use app\system\DB;

class Message
{

    private $db;

    CONST CUSTOMER = 'CLIENT';
    const SUPPORT = 'SUPPORT';

    public function __construct()
    {
        $this->db = new DB();
        $this->__table();
    }

    public function store($ticket_id, $sender, $message)
    {
        $this->db->insert([
            "ticket_id" => $ticket_id,
            "sender" => $sender,
            "message" => $message,
            "createdAt" => date("Y-m-d H:i:s")
        ], 'tickets_messages');
    }

    public function last($ticketId)
    {
        return $this->db->select("SELECT * FROM `tickets_messages` WHERE `ticket_id`=? ORDER BY `id` DESC LIMIT 1", [$ticketId])[0];
    }

    public function data($ticketId)
    {
        return $this->db->select("SELECT * FROM `tickets_messages` WHERE `ticket_id`=?", [$ticketId]);
    }

    private function __table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `tickets_messages` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `ticket_id` INT(11) NOT NULL , `sender` ENUM('CLIENT','SUPPORT') NOT NULL , `message` TEXT NOT NULL , `createdAt` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;");
    }

}