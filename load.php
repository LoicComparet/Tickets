<?php

    error_reporting(E_ALL | E_STRICT);

    /**
     * Requirements for the test
     */
    ini_set('memory_limit', '128M');
    ini_set('max_execution_time', 30);

    /**
     * We inspect time and memory for beginning
     */
    $begin_time = microtime(true);
    $begin_memory = memory_get_usage(true);

//@TODO modify the file 'config/db.php' for custom settings
    $configs = include __DIR__ . "/config/db.php";

    /**
     * Instanciate PDO connections
     */
    $pdo = new PDO("mysql:host=" . $configs['host'] . ";dbname=" . $configs['dbname'], $configs['user'],
        $configs['password'], array(
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ));

//We set transaction mode because it's faster and more secure
    $pdo->beginTransaction();

//Headers are useful only for grouping sanitize functions
    $headers = array('account', 'invoice', 'client', 'date_conso', 'hour_conso', 'real_data', 'bill_data', 'type_data');

    /**
     * We ensure that the received data is valid and filtered (OWASP TOP 10)
     */
    $sanityze_options = array_combine($headers, array(
        FILTER_VALIDATE_INT,
        FILTER_VALIDATE_INT,
        FILTER_VALIDATE_INT,
        FILTER_SANITIZE_STRING,
        FILTER_SANITIZE_STRING,
        FILTER_SANITIZE_STRING,
        FILTER_SANITIZE_STRING,
        FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    ));

    /**
     * We prepare the object for reading the file
     * \SplFileObject is the more efficient
     *  separator ';'
     */
    $splFileObject = new \SplFileObject(__DIR__ . "/data/csv/tickets_appels_201202.csv");
    $splFileObject->setCsvControl(";");
    $splFileObject->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::DROP_NEW_LINE);

    /**
     * We proceed to multiple insert SQL because it's more faster
     */

//Number of blocks inserted (max)
    $max_insert_blocks = 50;

//Parameters to send
    $data = array();

    /**
     * SQL pattern
     */
    $sql = 'INSERT INTO CONSO(ACCOUNT, INVOICE, CLIENT, DATE_CONSO, REAl_DATA, BILL_DATA, TYPE_DATA) VALUES(?,?,?,?,?,?,?)';
    $values = '(?,?,?,?,?,?,?)';

//For each line of the file
    foreach ($splFileObject as $row) {
        /**
         * Call to sanityze function
         */
        $row = array_combine($headers, $row);
        $filtered = filter_var_array($row, $sanityze_options, true);

        list($account, $invoice, $client, $date, $hour, $real, $bill, $type) = array_values($filtered);

        //To be processed, this data is required
        if ($account && $invoice && $client) {

            $date = DateTime::createFromFormat('d/m/Y H:i:s', "$date $hour");

            if (!$date) {
                /**
                 * Wrong date : problems with the data, we skip
                 * @TODO we could log later : not implemented here
                 */
                continue;
            }

            $data = array_merge($data, array(
                $account,
                $invoice,
                $client,
                $date->format('Y-m-d H:i:s'),
                $real,
                $bill,
                mb_convert_encoding($type, "UTF-8", mb_detect_encoding($type))
            ));

            $max_insert_blocks--;

            //If false, we can proceed to sent data to the db
            if (!$max_insert_blocks) {

                $stmt = $pdo->prepare($sql);
                $stmt->execute($data);
                unset ($stmt);

                //We go again
                $max_insert_blocks = 50;
                $data = array();
                $sql = 'INSERT INTO CONSO(ACCOUNT, INVOICE, CLIENT, DATE_CONSO, REAl_DATA, BILL_DATA, TYPE_DATA) VALUES(?,?,?,?,?,?,?)';
            } else {
                $sql .= ",$values";
            }
        } else {
            /**
             * Problems with the data, we skip
             * @TODO we could log later : not implemented here
             */
            continue;
        }
    }

    $pdo->commit();

    /**
     * We look at the time it took us
     */
    $end_time = microtime(true);
    $end_memory = memory_get_usage(true);

    print('Temps d\'execution : ' . ($end_time - $begin_time) . ' secondes et consommation memoire : ' . ($end_memory - $begin_memory) . ' octets');