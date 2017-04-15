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

    $res = $pdo->query("SELECT DISTINCT CLIENT FROM CONSO WHERE (DATE_CONSO NOT BETWEEN '% 08:00:00' AND '% 18:00:00') AND TYPE_DATA LIKE '%connexion%'",
        \PDO::FETCH_ASSOC);

    $data = array();

    foreach ($res->fetchAll() as $row) {
        $res2 = $pdo->query("SELECT * FROM CONSO WHERE CLIENT =" . $row['CLIENT'] . " ORDER BY BILL_DATA DESC LIMIT 10",
            \PDO::FETCH_ASSOC);

        if (!array_key_exists($row['CLIENT'], $data)) {
            $data[$row['CLIENT']] = array();
        }

        $data[$row['CLIENT']] = $res2->fetchAll();
    }

    echo json_encode($data);