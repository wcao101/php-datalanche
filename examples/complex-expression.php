<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';


function returnNewExpression()
{
    return new DLExpression();
}


$results = null;
$client = new DLClient($apiSecret, $apiKey);
$expression = new DLExpression();
$expression->boolAnd(
array(
    returnNewExpression()->boolOr(
        array(
                returnNewExpression()->column('col3')->equals('hello'),
                returnNewExpression()->column('col3')->equals('world')
            )),
            returnNewExpression()->column('col1')->equals('0f21b968-cd28-4d8b-9ea6-33dbcd517ec5')
    ));

$query = new DLQuery();
$query->select('*')->from('my_table')->where($expression);
$results = $client->query($query);

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