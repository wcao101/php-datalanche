<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

    function DLDropTable($secret, $key, $host, $port, $ssl, $tableName)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $query = new DLQuery();
        $query->dropTable($tableName);

        try
        {
            $results = $client->query($query);
        } catch (Exception $e) {
            echo $e."\n";
        }
    }


?>