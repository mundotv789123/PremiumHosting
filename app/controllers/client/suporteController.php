<?php

namespace app\controllers\client;

use app\libs\customers\Authentication;
use app\libs\customers\Profile;
use app\libs\tickets\Message;
use app\libs\tickets\Ticket;
use app\system\Controller;

class suporteController extends Controller
{

    public $title;
    public $data = [];

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

        $this->title = "Tickets";

        $this->view();
    }

    public function abrir()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }

        $this->title = "Abrir novo ticket";

        $this->view();
    }

    public function ler()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }

        $this->title = "Resumo do ticket";

        $id = explode('-', $this->getParams(0))[0];

        $ticket = new Ticket();
        $message = new Message();

        if(!$ticket->has($id)) {
            header('Location: /client/suporte');
            die();
        }

        $this->data = [
            'ticket' => $ticket->data(Authentication::id(), $id),
            'messages' => $message->data($id)
        ];

        $this->view();
    }

    public function actions()
    {
        if(!isset($_SESSION['HyMC@CustomerId']))
        {
            header("Location: /client/auth/login");
            die();
        }

        $method = $_SERVER['REQUEST_METHOD'];
        $ticket = new Ticket();

        switch ($method) {
            case 'POST':
                echo $ticket->store();
                break;
            case 'PUT':
                echo $ticket->reply(Message::CUSTOMER);
                break;
            case 'DELETE':
                echo $ticket->remove($this->getParams(0));
                break;
            default:
                echo($method.' METHOD NOT ALLOWED');
                break;
        }
    }

}