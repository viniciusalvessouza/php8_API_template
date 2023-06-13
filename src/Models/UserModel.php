<?php

namespace App\Models;

use App\Config\ResponseHTTP;
use App\Config\Security;
use App\DB\ConnectionDB;
use App\DB\Sql;
use Dotenv\Repository\Adapter\ReplacingWriter;

class UserModel extends ConnectionDB{

    private static string $name;   
    private static string $rg;      
    private static string $email;
    private static string $ocupacao;
    private static string $password;
    private static string $IDToken;  
    private static string $date;     

    public function __construct(array $data)
    {
        self::$name = $data['name'];
        self::$rg= $data['rg'];
        self::$email=$data['email'];
        self::$ocupacao=$data['ocupacao'];
        self::$password=$data['password'];
    }
    //getters
    final public static function getName(){ return self::$name;}
    final public static function getRg(){ return self::$rg;}
    final public static function getEmail(){ return self::$email;} 
    final public static function getOcupacao(){ return self::$ocupacao;}
    final public static function getPassword(){ return self::$password;}
    final public static function getIDToken(){ return self::$IDToken;}
    final public static function getdate(){ return self::$date;}
 
    //setters
    final public static function setName($name){self::$name = $name;}
    final public static function setRg($rg){self::$rg = $rg;}
    final public static function setEmail($email){self::$email = $email;}
    final public static function setOcupacao($ocupacao){self::$ocupacao = $ocupacao;}
    final public static function setPassword($password){self::$password = $password;}
    final public static function setIDToken($IDToken){self::$IDToken = $IDToken;}
    final public static function setDate($date){self::$date = $date;}

    //LOGIN
    final public static function login()
    {
        
        try {
            $conn = self::getConnection()->prepare("SELECT * FROM usuario WHERE email = :email");
            $conn->execute([
                ':email'=>self::getEmail()
            ]);
            
            if($conn->rowCount() === 0 ){
                return ResponseHTTP::status400('O usuario ou a senha estão incorretos');
            } else{
                foreach ($conn as $res) {
                    if(Security::validatePassword(self::getPassword(),$res['password'])){
                        $payload = ['IDToken' => $res['IDToken']];
                        $token = Security::createTokenJWT(Security::secretKey(),$payload);

                        $data = [
                            'name' => $res['name'],
                            'ocupacao' => $res['ocupacao'],
                            'token' => $token,
                        ];
                        //essa parte esta diferente dda dele, pois no meu response,a funcao soh aceita string e nao array
                        return ResponseHTTP::status200( $data);
                        exit;

                    } else{
                        return ResponseHTTP::status400('O usuario ou a senha estão incorretos ');
                    }
                }
            }

        } catch (\PDOException $e) {
            error_log("UserModel::Login() -> {$e}");
            die(json_encode(ResponseHTTP::status500()));
        }
      
    }

    final public static function validateUserPassword(string $IDToken, string $oldPassword)
    {
        try {
            $conn = self::getConnection();
            $query = $conn->prepare("SELECT * FROM usuario WHERE IDToken = :IDToken");
            $query->execute([
                ':IDToken'=>$IDToken
            ]);
            
            if($query->rowCount() === 0 ){
                return ResponseHTTP::status500('');
            } else{
                $res = $query->fetch(\PDO::FETCH_ASSOC);
                if(Security::validatePassword($oldPassword,$res['password'])){
                    return true;
                } else return false;
            }
        }
        catch(\PDOException $e){
            error_log("UserModel::validateUserPassword() -> {$e}");
            die(json_encode(ResponseHTTP::status500('nao foi possivel validar o usuario, tente novamente. Se o problema perssistir, contate o provedor')));

        }

    }

    //mostra todos os usuarios
    final public static function getAll()
    {
        try {
            $conn = self::getConnection();
            $query = $conn->prepare('SELECT * FROM usuario');
            $query->execute();
            $res['data'] = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $res;

        } catch (\PDOException $e) {
            error_log("UserModel::getAll -> {$e}");
            die(json_encode(ResponseHTTP::status500('nao foi possivel obter os dados')));
        }
    }

    //buscar usuario pelo rg
    final public static function getUser()
    {
        try {
            $conn = self::getConnection();
            $query = $conn->prepare("SELECT * FROM usuario WHERE rg = :rg");
            $query->execute([
                ':rg'=>self::getRg()
            ]);

            if($query->rowCount() == 0){
                return ResponseHTTP::status400('nao foi encontrado nenhum RG com esse valor');
            }else{  
                $res['data'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                return ResponseHTTP::status200($res);
            }


        } catch (\PDOException $e) {
            error_log("UserModel::getAll -> {$e}");
            die(json_encode(ResponseHTTP::status500('nao foi possivel obter os dados do usuario')));
        }
    }

    //inserir ususario via post
    final public static function postSave()
    {
        if(Sql::exists("SELECT rg FROM usuario WHERE RG = :rg",':rg',self::getRg()))
            return ResponseHTTP::status400('O rg ja esta registrado');

        else if(Sql::exists("SELECT email FROM usuario WHERE email = :email",':email',self::getEmail()))
            return ResponseHTTP::status400('O email ja esta registrado');

        else{
            self::setIDToken(hash('sha512',self::getRg().self::getEmail()));
            self::setDate(date("d-m-y H:i:s"));

            try {
                $conn = self::getConnection();
                $query1 = 'INSERT INTO usuario (name, rg, email, ocupacao, password,IDToken, data) VALUES';
                $query2 = '(:name, :rg, :email, :ocupacao, :password, :IDToken, :data)';
                $query = $conn->prepare($query1.$query2);
                $query->execute([
                    ':name'     =>self::getName(),
                    ':rg'       =>self::getRg(),
                    ':email'    =>self::getEmail(),
                    ':ocupacao' =>self::getOcupacao(),
                    ':password' =>Security::createPassword(self::getPassword()),
                    ':IDToken'  =>self::getIDToken(),
                    ':data'     =>self::getDate()
                ]);
                if($query->rowCount()>0)
                    return ResponseHTTP::status200('Usuario registrado com sucesso');
                else{
                    return ResponseHTTP::status500('Nao foi possivel registrar o usuario');
                }
            } catch (\PDOException $e) {
                error_log('UserModel::post() ->'.$e);
                die(json_encode(ResponseHTTP::status500()));
            }

        }
    }   
    
    final public static function patchPassword()
    {

        try {
            $conn = self::getConnection();
            $query =$conn->prepare('UPDATE usuario SET password = :password WHERE IDToken = :IDToken');
            $query->execute([
               ':password'=>Security::createPassword(self::getPassword()),
               ':IDToken'=>self::getIDToken()
            ]);

            if($query->rowCount() > 0 )
                return ResponseHTTP::status200('senha atualizada com sucesso');
            else 
                return ResponseHTTP::status500('erro ao atualizar a senha');

        } catch (\PDOException $e) {
            error_log("UserModel::patchPassword() -> {$e}");
            die(json_encode(ResponseHTTP::status500()));
        }

    }

    final public static function deleteUser()
    {
        try{
        $conn = self::getConnection();
        $query = $conn->prepare('DELETE FROM usuario WHERE IDToken = :IDToken');
        $query->execute([
            ':IDToken'=> self::getIDToken()
        ]);

        if($query->rowCount() >0)
            return ResponseHTTP::status200('usuario deletado');
        else
            return ResponseHTTP::status500(' nao foi possivel deletar o usuario');
            
        }catch(\PDOException $e){
            error_log("UserModel::deleteuser() -> {$e}");
            return ResponseHTTP::status500();
        }    
        
    }
}