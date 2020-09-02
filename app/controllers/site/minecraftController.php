<?php

namespace app\controllers\site;

use app\system\Controller;

class minecraftController extends Controller
{

    public $title;
    public $template;

    public function __construct()
    {
        parent::__construct();

        $this->setLayout("core");
    }

    public function single()
    {
        $this->title = 'Hospedagem de Minecraft';
        $this->template = [
          'subtitle' => 'Hospedagem Minecraft',
          'description' => 'Nosso objetivo é fornecer aos clientes uma hospedagem de alto desempenho no Minecraft com 100%<br>de tempo de atividade que atenda até os mais altos requisitos, mantendo assim um preço acessível.'
        ];

        return $this->view();
    }

    public function bungeecord()
    {
        $this->title = 'Combo Network';
        $this->template = [
            'subtitle' => 'Combo Network',
            'description' => 'Nosso objetivo é fornecer aos clientes uma Network de alto desempenho com 100%<br>de tempo de atividade que atenda até os mais altos requisitos.'
        ];

        return $this->view();
    }

}