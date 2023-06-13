<?php

namespace App\Config;

use App\Config\ResponseHTTP;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class Security{

    private static $jwt_data;

    final public static function secretKey()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__,2));
        $dotenv->load();
        return json_encode($_ENV['SECRET_KEY']);
    }
    
    final public static function createPassword(string $pw)
    {

        //esse PASSWORD_DEFAULT eh uma constante e pode mudar dependendo da versao
            //do php (pq podem surgir algoritmos melhores)
        $pass = password_hash($pw,PASSWORD_DEFAULT);
        return $pass;
    }

    final public static function validatePassword(string $pw, string $pwh)
    {
        if (password_verify($pw,$pwh)) {
            return true;            
        } else {
            error_log('o hash de senha esta incorreto');
            return false;
        }
    }

    final public static function createTokenJWT(string $key, array $data)
    {
        $payload  = array(
            "iat"=>time(),
            "exp"=>time() + 60*5, //tempo em segundos
            "data"=> $data
        );

        //esse algoritmo eh um de uma lista, para saber mais visite:
            //https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
        $jwt = JWT::encode($payload,$key,'HS256');
        
        return $jwt;
    }

    final public static function validateTokenJwt(array $token, string $key)
    {

        if (!isset($token['authorization'])){
            die(json_encode(ResponseHTTP::status400()));
            exit;
        }
        
        try {
            $jwt = explode(" ", $token['authorization']);
            $data = JWT::decode($jwt[1],new Key($key, 'HS256'));
            self::$jwt_data = $data;
            return $jwt; //$data;
            exit;
        } catch (\Exception $e) {
            error_log("Token invalido ou expirou. {$e}");
            die(json_encode(ResponseHTTP::status401("Token invalido ou expirado")));
        }
        
    }

    final public static function getDataJwt()
    {
        //o objetivo disso eh gerar um array associativo
        $jwt_decode_arrray =  json_decode(json_encode(self::$jwt_data,true));

        
        if(is_array($jwt_decode_arrray))
            return $jwt_decode_arrray['data'];
        else 
            return $jwt_decode_arrray;
        exit;
    }
}