<?php

namespace app\controllers\admin;

use app\libs\customers\Profile;
use app\system\Controller;

class homeController extends Controller
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

        $this->title = "Dashboard";

        $this->view();
    }

}