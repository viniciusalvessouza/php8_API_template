<?php

namespace App\Controllers;

use App\Config\ResponseHTTP;
use App\Config\Security;
use App\Models\UserModel;
use Random\Engine\Secure;

class UserController{
    //expressao regular
    private static $validate_ocupacao = ' /^[1,2,3]{1,1}$/';
    private static $validate_number = ' /^[0-9]+$/';
    private static $validate_text = ' /^[a-zA-Z]+$/';

    public function __construct(
        private string $method,
        private string $route,
        private array $params,
        private $data,
        private $headers
    )
    {
        
    }
    //rota auth para fazer login de usuario
    final public function getLogin(string $endPoint)
    {
        if($this->method == 'get' && $endPoint == $this->route){
            $email = strtolower($this->params[1]);
            $password = strtolower($this->params[2]);
            if(empty($email) || empty($password)){
                echo json_encode(ResponseHTTP::status400('Todos os campos sao necessarios'));
            }else if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                echo json_encode(ResponseHTTP::status400('formato de email invalido'));

            }else{
                UserModel::setEmail($email);
                UserModel::setPassword($password);

                echo json_encode(UserModel::login());
            }

            exit;
        }
    }

    final public function getAll(string $endPoint)
    {
        if($this->method == 'get' && $endPoint == $this->route){
            Security::validateTokenJwt($this->headers,Security::secretKey());
            
            echo json_encode(UserModel::getAll());

            exit;
        }
    }

    final public function getUser(string $endPoint)
    {

        if($this->method == 'get' && $endPoint == $this->route){
            Security::validateTokenJwt($this->headers,Security::secretKey());

            $rg = $this->params[1];
            if(!isset($rg)){    
                echo ResponseHTTP::status400('o campo cedula é requerido');
            } else if(!preg_match(self::$validate_number, $rg)){
                echo ResponseHTTP::status400("valor invalido para o Rg");
            } else {
                UserModel::setRg($rg);
                echo json_encode(UserModel::getUser());
                exit;
            }
               exit;     
        }
        else echo json_encode(ResponseHTTP::status400("UserController::getUser() ->metodo ou endPoint invalido"));

    }
    //salvar usuario usando o methodo post
    final public function postSave(string $endPoint)
    {        
        //validacao de tudo
        if($this->method == 'post' && $endPoint == $this->route){
            Security::validateTokenJwt($this->headers,Security::secretKey());
         
            if( empty($this->data['name'])    ||     
                empty($this->data['rg'])      || 
                empty($this->data['email'])   ||
                empty($this->data['ocupacao'])||
                empty($this->data['password'])||
                empty($this->data['confirmPassword'])                    
            ){
                echo json_encode(ResponseHTTP::status400('Todos os campos sao necessarios'));

            }else if(!preg_match(self::$validate_text, $this->data['name'])){
                echo json_encode(ResponseHTTP::status400('O campo nome nao permite numeros nem caracteres especiais'));
            
            }else if(!preg_match(self::$validate_number, $this->data['rg'])){
                echo json_encode(ResponseHTTP::status400('Preencha o RG apenas com numeros'));    
            
            }else if(!filter_var($this->data['email'],FILTER_VALIDATE_EMAIL)){
                echo json_encode(ResponseHTTP::status400('Formato de email invalido'));    
            
            }else if(!preg_match(self::$validate_ocupacao, $this->data['ocupacao'])){
                echo json_encode(ResponseHTTP::status400('Ocupacao invalida'));   
            
            }else if(strlen($this->data['password']) <8  || strlen($this->data['confirmPassword'] <8 )){
                echo json_encode(ResponseHTTP::status400('A senha deve conter no minimo 8 caracteres'));    
            
            }else if($this->data['password'] !== $this->data['confirmPassword']){
                echo json_encode(ResponseHTTP::status400('A senha e a confirmacao da senha nao sao iguais'));    
            
            }
            else {
                new UserModel($this->data);
                echo json_encode(UserModel::postSave());
            }

            exit;
        }      
    }

    //altera a senha do usuario
    final public function patchPassword(string $endPoint)
    {
        
        //validacao de tudo
        if($this->method == 'patch' && $endPoint == $this->route){
            Security::validateTokenJwt($this->headers,Security::secretKey());
            
            $jwtUserData =  Security::getDataJwt();

            if( empty($this->data['oldPassword'])    ||     
                empty($this->data['newPassword'])    || 
                empty($this->data['confirmNewPassword'])
            ){
                echo json_encode(ResponseHTTP::status400('Todos os campos sao necessarios'));

            }else if(!UserModel::validateUserPassword($jwtUserData->data->IDToken,$this->data['oldPassword'])){
                echo json_encode(ResponseHTTP::status400('A senha antiga nao é valida '));
            
            }else if(strlen($this->data['newPassword']) <8  || strlen($this->data['confirmNewPassword'] <8 )){
                echo json_encode(ResponseHTTP::status400('A nova senha deve conter no minimo 8 caracteres'));    
            
            }else if($this->data['newPassword'] !== $this->data['confirmNewPassword']){
                echo json_encode(ResponseHTTP::status400('A nova senha e a confirmacao da senha nao sao iguais'));    
            
            }
            else {
                UserModel::setIDToken($jwtUserData->data->IDToken);
                UserModel::setPassword($this->data['newPassword']);

                echo json_encode(UserModel::patchPassword());
            }

            exit;
        }      
    }

    final public function deleteUser(string $endPoint)
    {
        if($this->method == 'delete' && $endPoint == $this->route){         
            Security::validateTokenJwt($this->headers,Security::secretKey());

            if(empty($this->data['IDToken'])){
                echo json_encode(ResponseHTTP::status400('todos os campos sao requeridos'));
            } else{
                UserModel::setIDToken($this->data['IDToken']);
                UserModel::deleteUser();
            }
            exit;
        }
    }
    
}
