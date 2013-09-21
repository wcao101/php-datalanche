<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');
$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';
$host = 'your_host';
$port = 'wanted_port';
$ssl = 'verify_ssl';

    function DLGetTableList($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $query = new DLQuery();
        $query->getTableList();


        try
        {
            $results = $client->query($query);
        } catch (Exception $e) {
            echo $e."\n";
        }
    }

DLGetTableList($apiKey,$apiSecret, $host, $port, $ssl);

?>