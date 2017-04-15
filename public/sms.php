<?php
    /**
     * Created by IntelliJ IDEA.
     * User: Loïc
     * Date: 14/04/2017
     * Time: 22:20
     */

    error_reporting(E_ALL | E_STRICT);

    /**
     * Requirements for the test
     */
    ini_set('memory_limit', '128M');
    ini_set('max_execution_time', 30);

//@TODO modify the file 'config/db.php' for custom settings
    $configs = include __DIR__ . "/../config/db.php";

    /**
     * Instanciate PDO connections
     */
    $pdo = new PDO("mysql:host=" . $configs['host'] . ";dbname=" . $configs['dbname'], $configs['user'],
        $configs['password'], array(
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ));

    $seconds = 0;
    $limit = 2000;
    $offset = 0;

    $res = $pdo->query("SELECT COUNT(*) AS N FROM CONSO WHERE TYPE_DATA LIKE '%sms%'", \PDO::FETCH_ASSOC);

    echo $res->fetch()['N'];

