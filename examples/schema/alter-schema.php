<?php
//
// Alter the given schema's properties. Must have admin access for the given database.
//
// equivalent SQL:
// ALTER SCHEMA my_schema RENAME TO my_new_schema;
//
require_once(dirname(__FILE__) . '/../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->alterSchema('my_schema');
    $q->renameTo('my_new_schema');
    $q->description('my_new_schema description text');

    $client->query($q);

    echo "alter_schema succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
