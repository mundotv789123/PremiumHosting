<?php

namespace app\controllers\admin;

use app\libs\customers\Profile;
use app\libs\services\Plan;
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
        if(!Profile::isAdmin())
        {
            header("Location: /client");
            die();
        }

        $this->title = "Planos";

        $this->view();
    }

    public function edit()
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

        $this->title = "Editar plano";

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

        $plan = new Plan();

        switch ($method) {
            case 'POST':
                if($this->getParams(0) == 'edit') {
                    echo $plan->edit();
                }else{
                    echo $plan->store();
                }
                break;
            case 'DELETE':
                $plan->delete($this->getParams(0));
                break;
            default:
                echo('GET METHOD NOT ALLOWED');
                break;
        }
    }

}