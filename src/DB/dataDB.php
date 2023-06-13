<?php

use App\Config\ResponseHTTP;
use App\DB\ConnectionDB;


$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__,2));
$dotenv->load();

$data= array(
    'serverDB'=> $_ENV['SERVER_DB'],
    'user'    =>$_ENV['DB_USER'],
    'password'=>$_ENV['DB_PASSWORD'],
    'dbname'  =>$_ENV['DB_NAME'],
    'ip'      =>$_ENV['DB_IP'],
    'port'    =>$_ENV['DB_PORT']
);

foreach($data as $key=>$value){
    if(empty($value) && $key !== 'password'){
        error_log('Os campos do DB estao vazios');
        die(json_encode(ResponseHTTP::status500("Os campos do DB estao vazios $key")));
    }
    }
    if(strtolower($data['serverDB']) ==='mysql'){
        
        $host = 'mysql:host='.$data['ip'].'; port= '.$data['port'].'; dbname='.$data['dbname'].';';

        ConnectionDB::from( $host,$data['user'], $data['password']);

    } else if(strtolower($data['serverDB']) ==='sqlserver'){
        
        $host = 'sqlsrv:server='.$data['ip'].'; port= '.$data['port'].'; dbname='.$data['dbname'].';';

        ConnectionDB::from( $host,$data['user'], $data['password']);
    }
    
        





// <?php

// use App\DB\ConnectionDB;


// $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__,2));
// $dotenv->load();

// $data= array(
//     'user'    =>$_ENV['DB_USER'],
//     'password'=>$_ENV['DB_PASSWORD'],
//     'dbname'  =>$_ENV['DB_NAME'],
//     'host'    =>$_ENV['DB_HOST'],
//     'port'    =>$_ENV['DB_PORT']
// );

// $host = 'mysql:host='.$data['host'].'; port= '.$data['port'].'; dbname='.$data['dbname'].';';

// ConnectionDB::from( $host,$data['user'], $data['password']);
