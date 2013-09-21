<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');
$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';
$host = 'your_host';
$port = 'wanted_port';
$ssl = 'verify_ssl';

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

DLDeleteFrom($apiKey,$apiSecret, $host, $port, $ssl);


?>