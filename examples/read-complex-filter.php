<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

$apiKey = '16YNL0N2QVS9kx2y07MgcA==';//'your_api_key';
$apiSecret = '';//'your_api_secret';

try {
    $client = new DLClient($apiKey, $apiSecret, 'localhost', 4001, false);

    $readFilter = new DLFilter();
    $readFilter->boolAnd(array(
        new DLFilter()->boolOr(array(
            new DLFilter()->field('dosage_form')->equals('capsule'),
            new DLFilter()->field('dosage_form')->equals('tablet')
        )),
        new DLFilter()->field('product_type')->contains('esc')
    ));

    $params = new DLReadParams();
    $params->dataset = 'medical_codes_ndc';
    $params->filter = $readFilter;
    $params->limit = 5;

    $data = $client->read($params);

    echo json_encode($data) . "\n";
} catch (Exception $e) {
    echo $e . "\n";
}

?>
