<?php

namespace app\controllers\client;

use app\libs\services\Plan;
use app\libs\services\Service;
use app\libs\utils\JsonPretty;
use app\system\Controller;

class servicosController extends Controller
{

    public $title;

    public function __construct()
    {
        parent::__construct();

        $this->setLayout("core");
    }

    public function index()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }

        $this->title = "Serviços ativos";

        $this->view();
    }

    public function detalhes()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }

        $service = new Service();

        if($this->getParams(0) == 'renew') {
            echo $service->renew();
            die();
        }

        if(!$service->has($this->getParams(0))) {
            header('Location: /client/servicos');
            die();
        }

        $this->title = "Seu serviço";
        $this->view();
    }

    public function hire()
    {

        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            echo JsonPretty::encode(['success' => false, 'message' => 'Você precisa estar logado']);
            die();
        }

        $service = new Service();

        echo $service->store();
        die();
    }

    public function actions()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'POST':
                break;
            default:
                echo($method.' METHOD NOT ALLOWED');
                break;
        }
    }

}