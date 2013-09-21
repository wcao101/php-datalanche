<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';

$client = new DLClient($apiSecret, $apiKey);
$query = new DLQuery();
$results = null;
$query->alterTable('my_table');
$query->rename('my_new_table');
$query->description('my_new_table description text');
$query->isPrivate(false);
$query->license(array(
    'name' => 'new license name',
    'url' => 'http://new_license_name.com',
    'description' => 'new license descritpion text'
        ));

$query->sources(array(
    array(
        'name' => 'new source1',
        'url' => 'http://new_source1.com',
        'description' => 'new source1 description text'
        ),
     array(
        'name' => 'new source2',
        'url' => 'http://new_source2.com',
        'description' => 'new source2 description text'
        )
    ));
$query->addColumn(array(
    'name' => 'new_col',
    'data_type' => 'int32',
    'description' => 'new_col description text'
    ));
$query->dropColumn('col2');
$query->dropColumn('col3');
$query->alterColumn('col1', array('data_type'=>'string'));

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