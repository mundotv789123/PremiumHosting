<?php

namespace app\system;

class Routers
{

    protected $routers = [
        'site' => 'site',
        'client' => 'client',
        'admin' => 'admin'
    ];

    protected $routerOnRaiz = 'site';

    protected $onRaiz = true;

}