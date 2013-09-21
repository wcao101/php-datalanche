<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');
$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';
$host = 'your_host';
$port = 'wanted_port';
$ssl = 'verify_ssl';

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
    
DLDropTable($apiKey,$apiSecret, $host, $port, $ssl);

?>