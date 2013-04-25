<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';

try {
    $client = new DLClient($apiKey, $apiSecret);

    // Uses default parameters however "dataset" is required.
    $params = new DLReadParams();
    $params->dataset = 'medical_codes_ndc';

    $data = $client->read($params);

    echo json_encode($data) . "\n";
} catch (Exception $e) {
    echo $e . "\n";
}

?>
