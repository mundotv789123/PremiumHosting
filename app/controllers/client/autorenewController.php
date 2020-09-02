<?php

namespace app\controllers\client;

use app\libs\services\Service;
use app\system\Controller;

class autorenewController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $service = new Service();
        $service->cron();
    }
}