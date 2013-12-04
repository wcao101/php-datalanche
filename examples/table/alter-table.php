<?php
//
// Alter the given table's properties. Must have admin access for the given database.
//
// equivalent SQL:
//
// BEGIN TRANSACTION;
//
// ALTER TABLE my_schema.my_table
//     DROP COLUMN col2,
//     ALTER COLUMN col1 DROP NOT NULL,
//     ALTER COLUMN col1 SET DATA TYPE text,
//     ADD COLUMN new_col integer;
// ALTER TABLE my_schema.my_table RENAME COLUMN col3 TO col_renamed;
// ALTER TABLE my_schema.my_table RENAME TO my_new_table;
// ALTER TABLE my_schema.my_new_table SET SCHEMA my_new_schema;
//
// COMMIT;
//
require_once(dirname(__FILE__) . '/../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->alterTable('my_schema.my_table');
    $q->setSchema('my_new_schema');
    $q->renameTo('my_new_table');
    $q->description('my_new_table description text');
    $q->addColumn('new_col', array(
        'data_type' => array(
            'name' => 'integer'
        ),
        'description' => 'new_col description text'
    ));
    $q->alterColumn('col1', array(
        'data_type' => array(
            'name' => 'text'
        ),
        'description' => 'new col1 description text'
    ));
    $q->dropColumn('col2');
    $q->renameColumn('col3', 'col_renamed');

    $client->query($q);

    echo "alter_table succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
