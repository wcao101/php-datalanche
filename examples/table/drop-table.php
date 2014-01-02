<?php
//
// Drop the given table. Must have admin access for the given database.
//
// equivalent SQL:
// DROP TABLE my_schema.my_table CASCADE;
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->dropTable('my_schema.my_table');
    $q->cascade(true);

    $client->query($q);

    echo "drop_table succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
