<?php

namespace app\controllers\client;

use app\libs\customers\Authentication;
use app\libs\security\CSRF;
use app\system\Controller;

class authController extends Controller
{

    public $title;
    public $inputs;
    public $secret;

    public function __construct()
    {
        parent::__construct();

        $this->setLayout("auth");
    }

    public function index()
    {
        header('Location: /client/auth/login');
        die();
    }

    public function login()
    {
        if(isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client");
            die();
        }

        $this->title = 'Entre com sua conta';

        $csrf = new CSRF();

        $this->inputs = $csrf->inputs([ "email", "password" ], false);
        $this->secret = [
            "name" => $csrf->getTokenID(),
            "value" => $csrf->getToken()
        ];

        if(!empty($_POST))
        {
            $auth = new Authentication();

            echo $auth->login();
            die();
        }

        return $this->view();
    }

    public function register()
    {
        if(isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client");
            die();
        }

        $this->title = 'Criar nova conta';

        $csrf = new CSRF();

        $this->inputs = $csrf->inputs([ "name", "surname", "email", "password", "confirm" ], false);
        $this->secret = [
          "name" => $csrf->getTokenID(),
          "value" => $csrf->getToken()
        ];

        if(!empty($_POST))
        {
            $auth = new Authentication();

            echo $auth->register();
            die();
        }

        return $this->view();
    }

    public function logout()
    {
        unset($_SESSION['HyMC@CustomerId']);
        header("Location: /client/auth/login");
        die();
    }

}