<?php
//
// Create the given schema. Must have admin access for the given database.
//
// equivalent SQL:
// CREATE SCHEMA my_schema;
//
require_once(dirname(__FILE__) . '/../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->createSchema('my_schema');
    $q->description('my_schema description text');

    $client->query($q);

    echo "create_schema succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
