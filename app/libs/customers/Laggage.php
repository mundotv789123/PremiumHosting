<?php

namespace app\libs\customers;

use app\libs\plugins\Category;
use app\libs\plugins\License;
use app\libs\plugins\Plugin;
use app\libs\security\Ajax;
use app\libs\utils\JsonPretty;
use app\system\DB;

class Laggage
{

    private $db;

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

        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            return JsonPretty::encode(['success' => false, 'message' => 'Você precisa estar logado']);
        }

        if(empty($_POST['plugin_id']))
        {
            return JsonPretty::encode(['success' => false, 'message' => 'Plugin não informado']);
        }

        $plugin = new Plugin();
        $category = new Category();

        $data = $plugin->get($_POST['plugin_id']);
        $type = $category->type($data->category);

        if($type == 1) {
            $this->db->insert([
                "customer_id" => Authentication::id(),
                "plugin_id" => $_POST['plugin_id'],
                "createdAt" => date("Y-m-d H:i:s")
            ], "luggages");
        } else {
            if(Profile::wallet() < $data->price) {
                return JsonPretty::encode(['success' => false, 'message' => 'Você não possui saldo suficiente! Faça uma recarga']);
            }

            $this->db->insert([
                "customer_id" => Authentication::id(),
                "plugin_id" => $_POST['plugin_id'],
                "createdAt" => date("Y-m-d H:i:s")
            ], "luggages");

            $wallet = new Wallet();
            $wallet->remove($data->price, Authentication::id());
        }

        $license = new License();
        $license->store(Authentication::id(), $_POST['plugin_id']);

        return JsonPretty::encode(['success' => true, 'message' => 'Plugin instalado! Vá até o seu painel para configurá-lo']);
    }

    public static function hasInstalled($id)
    {
        $db = new DB();
        $customerId = Authentication::id();
        return $db->select("SELECT * FROM `luggages` WHERE `customer_id`={$customerId} AND `plugin_id`={$id}");
    }

    private function __table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `luggages` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `customer_id` INT(11) NOT NULL , `plugin_id` INT(11) NOT NULL , `createdAt` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;";
        $this->db->query($sql);
    }

}