<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');
$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';


$results = null;
$client = new DLClient($apiSecret, $apiKey);
$query = new DLQuery();
$query->dropTable('my_table');

try
{
    $results = $client->query($query);
} catch (Exception $e) {
    echo $e."\n";
}

if($results['response']['headers']['http_code'] === 200)
{
    echo "SUCCESS!!\n";
}


?>