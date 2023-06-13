<?php

namespace App\Config;
/**
 * @author Vinicius souza
 * 
 */
class ResponseHTTP{
    
    public static $message = array(
        'status'=>'',
        'message' =>''
    );

    final public static function status200(string|array $res){
        http_response_code(200);
        self::$message['status'] = 'ok';
        self::$message['message'] = $res;
        return self::$message;
    }

    final public static function status201(string $res){
        http_response_code(201);
        self::$message['status'] = 'ok';
        self::$message['message'] = $res;
        return self::$message;
    }

    final public static function status400(string $res ='Requisicao incompleta ou formato de dados errado'){
        http_response_code(400);
        self::$message['status'] = 'error';
        self::$message['message'] = $res;
        return self::$message;
    }

    final public static function status401(string $res ='Nao possui privilegios para acessar esse recurso'){
        http_response_code(401);
        self::$message['status'] = 'error';
        self::$message['message'] = $res;
        return self::$message;
    }

    final public static function status404(string $res ='Recurso nao encontrado, por favor verifique a documentacao'){
        http_response_code(404);
        self::$message['status'] = 'error';
        self::$message['message'] = $res;
        return self::$message;
    }

    final public static function status500(string $res ='Erro interno do servidor'){
        http_response_code(500);
        self::$message['status'] = 'error';
        self::$message['message'] = $res;
        return self::$message;
    }

    
}