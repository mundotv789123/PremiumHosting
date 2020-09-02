<?php

namespace app\controllers\admin;

use app\libs\customers\Profile;
use app\libs\tickets\Message;
use app\libs\tickets\Ticket;
use app\system\Controller;

class ticketsController extends Controller
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

        $this->title = "Tickets";

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

        $this->data = [
            'ticket' => $ticket->data(null, $id),
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
        if(!Profile::isAdmin())
        {
            header("Location: /client");
            die();
        }

        $method = $_SERVER['REQUEST_METHOD'];
        $ticket = new Ticket();

        switch ($method) {
            case 'PUT':
                echo $ticket->reply(Message::SUPPORT);
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