<?php

@session_start();

header_remove( 'X-Powered-By' );
header("X-XSS-Protection: 1; mode=block");
header("X-WebKit-CSP: policy");
header('Content-Type: text/html; charset=utf-8');

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

require_once 'app/system/Autoload.php';

use app\system\App;
use app\system\Config;

set_time_limit(0);
ini_set('display_errors', (Config::SHOW_ERRORS) ? 1 : 0);
ini_set('display_startup_erros', (Config::SHOW_ERRORS) ? 1 : 0);
error_reporting(E_ALL);

if(Config::HTTPS_FORCE) {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        if(!headers_sent()) {
            header("Status: 301 Moved Permanently");
            header(sprintf(
                'Location: https://%s%s',
                $_SERVER['HTTP_HOST'],
                $_SERVER['REQUEST_URI']
            ));
            exit();
        }
    }
}

if(Config::HTML_MINIFY) {
    function sanitize_output($buffer) {

        $search = array(
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s',
            '/<!--(.|\s)*?-->/'
        );

        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

    ob_start("sanitize_output");
}

if (! empty($_SERVER['HTTPS'])) {
    $config['base_url'] = 'https://'.Config::DOMAIN.'/';
} else {
    $config['base_url'] = 'http://'.Config::DOMAIN.'/';
}

require_once 'vendor/autoload.php';

define('APP_ROOT', $config['base_url']);

$app = new App();

$app->serve();