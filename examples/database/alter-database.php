<?php
//
// Alter the given database's properties. Must have admin access for the database.
//
// equivalent SQL:
// ALTER DATABASE my_database RENAME TO my_new_database;
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery();
    $q->alterDatabase('my_database');
    $q->renameTo('my_new_database');
    $q->description('my_new_database description text');

    $client->query($q);

    echo "alter_database succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
