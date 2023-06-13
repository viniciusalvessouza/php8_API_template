<?php

use App\Config\ErrorLog;
use App\Config\ResponseHTTP;

require dirname(__DIR__).'/vendor/autoload.php';

ErrorLog::activeErrorLog();

if(isset($_GET['route'])){
    $url=explode('/',$_GET['route']);
    //lista das rotas permitidas
    $lista  = ['Auth','User'];
    $file = dirname(__DIR__)."/src/Routes/$url[0].php";

    if(!in_array($url[0],$lista)){
        echo json_encode(ResponseHTTP::status400());
        error_log('nao foi possivel conectar por erro do cliente');
        exit;
    }

    if(is_readable($file)){
        require $file;
        exit;
    } else echo json_encode(ResponseHTTP::status400());

} else 
    echo json_encode(ResponseHTTP::status404());
