<?php

use App\Config\ResponseHTTP;
use App\Controllers\UserController;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'];
$params = explode('/',$route);
$data =json_decode(file_get_contents('php://input'),true);
$headers = getallheaders();

$app = new UserController($method,$route,$params,$data, $headers);

$app->getLogin("Auth/{$params[1]}/{$params[2]}/");

//echo json_encode('final do codigo');

echo json_encode(ResponseHTTP::status404());