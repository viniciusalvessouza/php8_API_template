<?php

use App\Config\ResponseHTTP;
use App\Controllers\UserController;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'];
$params = explode('/',$route);
$data =json_decode(file_get_contents('php://input'),true);
$headers = getallheaders();

//------------------------- instancias da controller -------------------------
$app = new UserController($method,$route,$params,$data, $headers);

//------------------------- rotas ------------------------- 
$app->getAll('User/');
//a rota estaticas tem que ficar antes das rotas dinamicas para nao dar erro
$app->patchPassword('User/Password/');
$app->deleteUser('User/');
$app->getUser("User/{$params[1]}");
$app->postSave('User/');
$app->postSave('User/');



//------------------------- erro 4040 -------------------------
echo json_encode(ResponseHTTP::status404('Pagina nao encontrada em User/, por favor verifique se o enderço foi digitado corretamente ou confira a documentacao'));