<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';

try {
    $client = new DLClient($apiKey, $apiSecret);

    $f1 = new DLFilter();
    $f1->field('dosage_form')->equals('capsule');

    $f2 = new DLFilter();
    $f2->field('dosage_form')->equals('tablet');

    $f3 = new DLFilter();
    $f3->boolOr(array($f1, $f2));

    $f4 = new DLFilter();
    $f4->field('product_type')->contains('esc');

    $readFilter = new DLFilter();
    $readFilter->boolAnd(array($f3, $f4));

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
