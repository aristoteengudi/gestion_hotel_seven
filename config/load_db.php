<?php

$params_db  = $config['database'];
$db_name    = $params_db['db_name'];
$db_user    = $params_db['db_user'];
$db_pwd     = $params_db['db_pwd'];
$db_host    = $params_db['db_host'];
$driver     = $params_db['driver'];
$port       = $params_db['port'];

$connectionParams = array(
    'dbname'    => $db_name,
    'user'      => $db_user,
    'password'  => $db_pwd,
    'host'      => $db_host,
    'driver'    => $driver,
    'port'      => $port,
    'charset'      => 'utf8'
);


/** @var \Doctrine\DBAL\Connection $db*/
$db = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);