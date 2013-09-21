<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');
$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';

$results = null;
$client = new DLClient($apiSecret, $apiKey);
$expression = new DLExpression();
$expression->column('col3')->contains('hello');
$query = new DLQuery();
$query->select(array('col1', 'col2'));
$query->from('my_table');
$query->where($expression);
$query->orderBy(
    array(
        array( 'col1' => '$asc' ),
        array( 'col2' => '$desc' )
        ));
$query->offset(0);
$query->limit(10);
$query->total(true);

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