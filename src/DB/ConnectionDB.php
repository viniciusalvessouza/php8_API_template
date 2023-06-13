<?php

namespace App\DB;

use App\Config\ResponseHTTP;
use PDO;

require __DIR__.'/dataDB.php';
/**
 * @description classe para conectar  e operar com o banco de dados
 */
class ConnectionDB{
    
    private static $host= '';
    private static $user= '';
    private static $password = '';

    /**
     * @param string $host os dados do servidor
     * @param string $user o usuario que conecta ao banco
     * @param string $password a senha para conectar com o usuario
     */
    final public static function from(string $host,string $user,string $password):void
    {
        self::$host = $host;
        self::$user = $user;
        self::$password = $password;

    }
    /**
     * @description esse metodo deve ser chamado apos o method from(), pois 
     * ira conectar a partir dos parametros definidos nele ou com parametros vazios
     * @return PDO|error em caso de sucesso a propria conexao, em caso de erro uma mensagem de erro
     */
    final public static function getConnection()
    {
        try {
            $opt = [\PDO::ATTR_DEFAULT_FETCH_MODE=> \PDO::FETCH_ASSOC];
            $dsn = new PDO(self::$host,self::$user,self::$password, $opt);
            $dsn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            error_log('conexao feita com sucesso');
            return $dsn;
        } catch (\PDOException $p) {
            error_log('Erro de conexão'. $p);
            die(json_encode(ResponseHTTP::status500()));
        }
    }


}