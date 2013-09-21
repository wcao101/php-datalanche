<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');
$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';


$results = null;
$client = new DLClient($apiSecret, $apiKey);
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

if($results['response']['headers']['http_code'] === 200)
{
    echo "SUCCESS!!\n";
}


?>