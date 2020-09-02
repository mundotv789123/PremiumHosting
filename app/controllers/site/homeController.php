<?php

namespace app\controllers\site;

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
        $this->title = 'Página inicial';
        return $this->view();
    }

}