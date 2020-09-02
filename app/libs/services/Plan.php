<?php

namespace app\libs\services;

use app\libs\security\Ajax;
use app\libs\utils\JsonPretty;
use app\system\DB;

class Plan
{

    private $db;

    public function __construct()
    {
        $this->db = new DB();
        $this->__table();
    }

    public function  store()
    {
        if(!Ajax::check()) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }

        if(empty($_POST['name']) || empty($_POST['price']) || empty($_POST['type']) || empty($_POST['data'])) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Informe todos os dados' ]);
        }

        $data = [];

        foreach ($_POST['data'] as $el) {
            $data[] = $el;
        }

        $data = json_encode($data);

        $this->db->insert([
            'name' => $_POST['name'],
            'price' => $_POST['price'],
            'data' => $data,
            'type' => $_POST['type']
        ], 'plans');

        return JsonPretty::encode([ 'success' => true, 'message' => 'Plano criado com sucesso!' ]);
    }

    public function edit()
    {
        if(!Ajax::check()) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }

        if(empty($_POST['name']) || empty($_POST['price']) || empty($_POST['type']) || empty($_POST['data'])) {
            return JsonPretty::encode([ 'success' => false, 'message' => 'Informe todos os dados' ]);
        }

        $data = [];

        foreach ($_POST['data'] as $el) {
            $data[] = $el;
        }

        $data = json_encode($data);

        $this->db->update([
            'name' => $_POST['name'],
            'price' => $_POST['price'],
            'data' => $data,
            'type' => $_POST['type']
        ], [ 'id' => $_POST['id'] ], 'plans');

        return JsonPretty::encode([ 'success' => true, 'message' => 'Plano editado com sucesso!' ]);
    }

    public function delete($id)
    {
        return $this->db->delete([ 'id' => $id ], 'plans');
    }

    public function data($id = null, $type = null)
    {
        if($type) {
            return $this->db->select("SELECT * FROM `plans` WHERE `type`=?", [$type]);
        }

        if($id) {
           return $this->db->select("SELECT * FROM `plans` WHERE `id`=?", [$id])[0];
        }

        return $this->db->select("SELECT * FROM `plans`");
    }

    private function __table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `plans` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(60) NOT NULL , `price` DECIMAL(10,2) NOT NULL , `data` JSON NOT NULL , `type` ENUM('MINECRAFT_SINGLE','MINECRAFT_BUNGEECORD','CLOUD_COMPUTING') NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;");
    }

}