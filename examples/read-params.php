<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';

try {
    $client = new DLClient($apiKey, $apiSecret);

    $params = new DLReadParams();
    $params->columns = array('dosage_form', 'route', 'product_type');
    $params->limit = 5;
    $params->skip = 0;
    $params->total = false;
    $params->filter = NULL;     // look at read-filter.php and read-complex-filter.php

    $params->sortDesc('dosage_form');
    $params->sortAsc('product_type');

    // You can also set $params->sort to an array instead of using the helper methods.
    $params->sort = array('dosage_form:$desc', 'product_type:$asc');

    $data = $client->readRecords('medical_codes_ndc', $params);

    echo json_encode($data) . "\n";
} catch (Exception $e) {
    echo $e . "\n";
}

?>
