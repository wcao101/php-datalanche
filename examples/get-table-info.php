<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

    function DLGetTableInfo($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $query = new DLQuery();
        $query->getTableInfo('my_table');


        try
        {
            $results = $client->query($query);
        } catch (Exception $e) {
            echo $e."\n";
        }
    }


?>