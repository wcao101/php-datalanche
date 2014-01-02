<?php
//
// Show all databases you have access to.
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery();
    $q->showDatabases();

    $result = $client->query($q);

    print_r($result);

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
