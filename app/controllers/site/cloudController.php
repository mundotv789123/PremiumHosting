<?php

namespace app\controllers\site;

use app\system\Controller;

class cloudController extends Controller
{

    public $title;
    public $template;

    public function __construct()
    {
        parent::__construct();

        $this->setLayout("core");
    }

    public function index()
    {
        $this->title = 'Cloud Computing';
        $this->template = [
            'subtitle' => 'Servidores Cloud',
            'description' => 'Oferecemos os servidores cloud mais poderosos do mercado, esta é uma ótima opção para quem<br> deseja executar os demais projetos e ter todos os recursos apenas para ele!'
        ];

        return $this->view();
    }

}