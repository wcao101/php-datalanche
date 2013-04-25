<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';

try {
    $client = new DLClient($apiKey, $apiSecret);

    $data = $client->getSchema('medical_codes_ndc');

    echo json_encode($data) . "\n";
} catch (Exception $e) {
    echo $e . "\n";
}

?>
