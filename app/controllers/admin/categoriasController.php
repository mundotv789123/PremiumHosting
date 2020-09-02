<?php

namespace app\controllers\admin;

use app\libs\customers\Profile;
use app\libs\plugins\Category;
use app\system\Controller;

class categoriasController extends Controller
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

        $this->title = "Categorias";

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

        $category = new Category();

        switch ($method) {
            case 'POST':
                echo $category->store();
                break;
            case 'DELETE':
                $category->remove($this->getParams(0));
                break;
            default:
                echo('GET METHOD NOT ALLOWED');
                break;
        }
    }

}