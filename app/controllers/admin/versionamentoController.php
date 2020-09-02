<?php

namespace app\controllers\admin;

use app\libs\customers\Profile;
use app\libs\plugins\Version;
use app\libs\utils\JsonPretty;
use app\system\Controller;

class versionamentoController extends Controller
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

        $this->title = "Versionamento";

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

        $version = new Version();

        switch ($method) {
            case 'POST':
                echo $version->store();
                break;
            case 'DELETE':
                $version->remove($this->getParams(0));
                break;
            default:
                echo('GET METHOD NOT ALLOWED');
                break;
        }

        die();
    }

}