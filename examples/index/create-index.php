<?php
//
// Create an index on the given table. Must have admin access for the given database.
//
// equivalent SQL:
// CREATE UNIQUE INDEX my_index ON my_schema.my_table USING btree (col1, col2);
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->createIndex('my_index');
    $q->unique(true);
    $q->onTable('my_schema.my_table');
    $q->usingMethod('btree');
    $q->columns(array('col1', 'col2'));

    $client->query($q);

    echo "create_index succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
