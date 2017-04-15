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

    $SELECT_BORNE = $pdo->prepare("SELECT TIME_TO_SEC(REAL_DATA) AS DUREE FROM CONSO WHERE DATE_CONSO >= '2012-02-15 00:00:00' AND TYPE_DATA LIKE '%appel%' LIMIT :limit OFFSET :offset");
    $SELECT_BORNE->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $SELECT_BORNE->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $SELECT_BORNE->execute();

    while ($SELECT_BORNE->rowCount()) {

        $row = $SELECT_BORNE->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            break;
        }

        do {
            $seconds += $row['DUREE'];
            $row = $SELECT_BORNE->fetch(PDO::FETCH_ASSOC);
        } while ($row);

        $offset += $limit;

        $SELECT_BORNE = $pdo->prepare("SELECT TIME_TO_SEC(REAL_DATA) AS DUREE FROM CONSO WHERE DATE_CONSO >= '2012-02-15 00:00:00' AND TYPE_DATA LIKE '%appel%' LIMIT :limit OFFSET :offset");
        $SELECT_BORNE->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $SELECT_BORNE->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $SELECT_BORNE->execute();
    }

    echo $seconds;