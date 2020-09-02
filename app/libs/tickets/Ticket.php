<?php

namespace app\libs\tickets;

use app\libs\customers\Authentication;
use app\libs\security\Ajax;
use app\libs\utils\JsonPretty;
use app\libs\utils\QueryParser;
use app\system\DB;

class Ticket
{

    private $db;
    const STATUS = [
        'Aguardando resposta',
        'Respondido',
        'Fechado'
    ];

    public function __construct()
    {
        $this->db = new DB();
        $this->__table();
    }

    public function store()
    {
        if(!Ajax::check())
        {
            return JsonPretty::encode(['success' => false, 'message' => 'Blocked']);
        }
        if(empty($_POST['subject']) || empty($_POST['message']))
        {
            return JsonPretty::encode(['success' => false, 'message' => 'Informe o assunto e a mensagem']);
        }

        $ticket = $this->db->insert([
            'owner' => Authentication::id(),
            'subject' => $_POST['subject'],
            'status' => 1
        ], 'tickets');

        $message = new Message();
        $message->store($ticket['id'], Message::CUSTOMER, nl2br($_POST['message']));

        return JsonPretty::encode(['success' => true, 'message' => 'Ticket aberto']);
    }

    public function reply($sender)
    {
        if(!Ajax::check())
        {
            return JsonPretty::encode(['success' => false, 'message' => 'Blocked']);
        }

        $putfp = fopen('php://input', 'r');
        $putData = '';

        while($data = fread($putfp, 1024)) {
            $putData .= $data;
        }

        fclose($putfp);

        $_PUT = QueryParser::decode($putData);

        if(empty($_PUT['message']))
        {
            return JsonPretty::encode(['success' => false, 'message' => 'Informe a mensagem']);
        }

        $message = new Message();
        $message->store($_PUT['id'], $sender, nl2br(urldecode($_PUT['message'])));

        $this->db->update([ 'status' => $sender == 'CLIENT' ? 1 : 2 ], [ 'id' => $_PUT['id'] ], 'tickets');

        return JsonPretty::encode(['success' => true, 'message' => 'Mensagem enviada!']);
    }

    public function remove($id)
    {
        if(!Ajax::check())
        {
            return JsonPretty::encode(['success' => false, 'message' => 'Blocked']);
        }

        $this->db->update([
            'status' => 3
        ], [
            'id' => $id,
            'owner' => Authentication::id()
        ], 'tickets');

        return JsonPretty::encode(['success' => true, 'message' => 'Ticket fechado']);
    }

    public function has($id)
    {
        return $this->db->select("SELECT * FROM `tickets` WHERE `id`=? AND `owner`=?", [$id, Authentication::id()]);
    }

    public function data($owner = null, $ticketId = null)
    {
        if($ticketId) {
            if(!$owner) {
                return $this->db->select("SELECT * FROM `tickets` WHERE `id`=?", [$ticketId])[0];
            }
            return $this->db->select("SELECT * FROM `tickets` WHERE `owner`=? AND `id`=?", [$owner, $ticketId])[0];
        }

        if($owner) {
            return $this->db->select("SELECT * FROM `tickets` WHERE `owner`=? ORDER BY `id` DESC", [$owner]);
        }

        return $this->db->select("SELECT * FROM `tickets` ORDER BY `id` DESC");
    }

    private function __table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `tickets` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `owner` INT(11) NOT NULL , `subject` VARCHAR(60) NOT NULL , `status` INT(1) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = MyISAM;");
    }

}