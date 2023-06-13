<?php

namespace App\DB;

use App\Config\ResponseHTTP;

class Sql extends ConnectionDB{
    
    public static function exists(string $request, string $condition, string $param){
        try {
            //herdado
            $conn = self::getConnection();
            $query = $conn->prepare($request);
            $query->execute([
                $condition =>$param,
            ]);

            $res = ($query->rowCount() ==0)? false : true;
            
            return $res;

        } catch (\PDOException $e) {
            error_log('SQL::exists->'.$e);
            die(json_encode(ResponseHTTP::status500()));
        }
    }
}