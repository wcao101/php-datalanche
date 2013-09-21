<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

    function DLDeleteFrom($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $columnComparison = new DLExpression();
        $columnComparison->column('col3')->equals('hello');
        $query = new DLQuery();
        $query->deleteFrom('my_table');
        $query->where($columnComparison);

        try
        {
            $results = $client->query($query);
        } catch (Exception $e) {
            echo $e."\n";
        }
    }


?>