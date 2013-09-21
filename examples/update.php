<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');
$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';
$host = 'your_host';
$port = 'wanted_port';
$ssl = 'verify_ssl';


    function DLUpdate($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $expression = new DLExpression();
        $expression->column('col3')->equals('hello');

        $query = new DLQuery();
        $query->update('my_table');
        $query->set(array('col3' => 'hello world'));
        $query->where($expression);
        try
        {
            $results = $client->query($query);
        } catch (Exception $e) {
            echo $e."\n";
        }
    }

DLUpdate($apiKey,$apiSecret, $host, $port, $ssl);
?>