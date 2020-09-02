<?php

namespace app\controllers\admin;

use app\libs\customers\Profile;
use app\libs\services\Service;
use app\system\Controller;

class pendentesController extends Controller
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
        if(!Profile::isAdmin())
        {
            header("Location: /client");
            die();
        }

        $this->title = "Ativação pendente";

        $this->view();
    }

    public function active()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }
        if(!Profile::isAdmin())
        {
            header("Location: /client");
            die();
        }

        $this->title = "Ativar serviço";

        $this->view();
    }

    public function actions()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }
        if(!Profile::isAdmin())
        {
            header("Location: /client");
            die();
        }

        $method = $_SERVER['REQUEST_METHOD'];

        $service = new Service();

        switch ($method) {
            case 'POST':
                echo $service->active();
                break;
            default:
                echo('GET METHOD NOT ALLOWED');
                break;
        }
    }

}