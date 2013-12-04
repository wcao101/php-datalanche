<?php
//
// Alter the given index's properties. Must have admin access for the given database.
//
// equivalent SQL:
// ALTER INDEX my_schema.my_index RENAME TO my_new_index;
//
require_once(dirname(__FILE__) . '/../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->alterIndex('my_schema.my_index');
    $q->renameTo('my_new_index');

    $client->query($q);

    echo "alter_index succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
