<?php

namespace app\libs\customers;

use app\libs\security\Ajax;
use app\libs\security\CSRF;
use app\libs\security\SQLi;
use app\libs\security\XSS;
use app\libs\utils\JsonPretty;
use app\libs\utils\Mail;
use app\libs\utils\RemoteAddress;
use app\system\DB;

class Authentication
{

    private $db;

    public function __construct()
    {
        $this->db = new DB();

        $this->__table();
    }

    public function login()
    {
        if(!Ajax::check()) {
            return JsonPretty::encode(["success" => false, "message" => "Blocked"]);
        }

        $csrf = new CSRF();
        $inputs = $csrf->getInputs();

        if(!$csrf->check($_POST)) {
            return JsonPretty::encode(["success" => false, "message" => "Não autorizado"]);
        }

        if(empty($_POST[$inputs['i-email']]) || empty($_POST[$inputs['i-password']])) {
            return JsonPretty::encode(["success" => false, "message" => "Informe seu usuário e senha"]);
        }

        $password = SQLi::filter_password($_POST[$inputs['i-password']]);

        $select = $this->db->select("SELECT * FROM `customers` WHERE `email`=?", [
            $_POST[$inputs['i-email']]
        ]);

        if(!$select)
        {
            return JsonPretty::encode(["success" => false, "message" => "E-mail não registrado"]);
        }

        if(!password_verify($password, $select[0]->password))
        {
            return JsonPretty::encode(["success" => false, "message" => "Senha inválida"]);
        }

        $_SESSION['HyMC@CustomerId'] = $select[0]->id;

        return JsonPretty::encode(["success" => true, "message" => "Autenticado com sucesso", "isAdmin" => Profile::isAdmin() ]);
    }

    public function register()
    {
        if(!Ajax::check()) {
            return JsonPretty::encode(["success" => false, "message" => "Blocked"]);
        }

        $csrf = new CSRF();
        $inputs = $csrf->getInputs();

        if(!$csrf->check($_POST)) {
            return JsonPretty::encode(["success" => false, "message" => "Não autorizado"]);
        }

        if(empty($_POST[$inputs['i-name']]) || empty($_POST[$inputs['i-surname']]) ||  empty($_POST[$inputs['i-email']]) || empty($_POST[$inputs['i-password']]) || empty($_POST[$inputs['i-confirm']])) {
            return JsonPretty::encode(["success" => false, "message" => "Informe todos os dados."]);
        }

        $name = XSS::filter($_POST[$inputs['i-name']]);
        $surname = XSS::filter($_POST[$inputs['i-surname']]);
        $email = XSS::filter($_POST[$inputs['i-email']]);
        $password = SQLi::filter_password($_POST[$inputs['i-password']]);
        $confirm = SQLi::filter_password($_POST[$inputs['i-confirm']]);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return JsonPretty::encode(["success" => false, "message" => "Informe um e-mail válido!"]);
        }

        if(Profile::hasEmail($email)) {
            return JsonPretty::encode(["success" => false, "message" => "E-mail já cadastrado!"]);
        }

        if($password != $confirm) {
            return JsonPretty::encode(["success" => false, "message" => "As senhas não coincidem!"]);
        }

        $insert = $this->db->insert([
            "name" => $name,
            "surname" => $surname,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_BCRYPT),
            "ip" => RemoteAddress::get(),
            "createdAt" => date("Y-m-d")
        ], "customers");

        $html = file_get_contents('./app/templates/emails/register.html');
        Mail::send($email, "Bem-vindo à plataforma!", $html);

        $_SESSION['HyMC@CustomerId'] = $insert['id'];

        return JsonPretty::encode(["success" => true, "message" => "Autenticado com sucesso!"]);
    }

    public static function id()
    {
        return $_SESSION['HyMC@CustomerId'];
    }

    private function __table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `customers` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(60) NOT NULL , `surname` VARCHAR(60) NOT NULL , `email` VARCHAR(120) NOT NULL , `password` VARCHAR(255) NOT NULL , `ip` VARCHAR(15) NOT NULL , `wallet` DECIMAL(10,2) NOT NULL DEFAULT '0', `admin` BOOLEAN NOT NULL DEFAULT FALSE, `createdAt` DATE NOT NULL, PRIMARY KEY (`id`)) ENGINE = MyISAM;";
        $this->db->query($sql);
    }
}