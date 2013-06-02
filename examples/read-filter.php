<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';

try {
    $client = new DLClient($apiKey, $apiSecret);

    $readFilter = new DLFilter();
    $readFilter->field('dosage_form')->notEquals('capsule');

    $params = new DLReadParams();
    $params->filter = $readFilter;
    $params->limit = 5;

    $data = $client->readRecords('medical_codes_ndc', $params);

    echo json_encode($data) . "\n";
} catch (Exception $e) {
    echo $e . "\n";
}

?>
