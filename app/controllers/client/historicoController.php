<?php

namespace app\controllers\client;

use app\system\Controller;

class historicoController extends Controller
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

        $this->title = "Histórico Financeiro";

        $this->view();
    }

}