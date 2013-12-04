<?php
//
// Drop the given schema. Must have admin access for the given database.
//
// equivalent SQL:
// DROP SCHEMA my_schema CASCADE;
//
require_once(dirname(__FILE__) . '/../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->dropSchema('my_schema');
    $q->cascade(true);

    $client->query($q);

    echo "drop_schema succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
