<?php
//
// Create the given table. Must have admin access for the given database.
//
// equivalent SQL:
// CREATE TABLE my_schema.my_table(
//     col1 uuid NOT NULL,
//     col2 varchar(50),
//     col3 integer DEFAULT 0 NOT NULL
// );
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->createTable('my_schema.my_table');
    $q->description('my_table description text');
    $q->columns(array(
        'col1' => array(
            'data_type' => array(
                'name' => 'uuid'
            ),
            'description' => 'col1 description text',
            'not_null' => true
        ),
        'col2' => array(
            'data_type' => array(
                'name' => 'varchar',
                'args' => array(50)
            ),
            'description' => 'col2 description text',
            'default_value' => NULL,
            'not_null' => false
        ),
        'col3' => array(
            'data_type' => array(
                'name' => 'integer'
            ),
            'description' => 'col3 description text',
            'default_value' => 0,
            'not_null' => true
        )
    ));

    $client->query($q);

    echo "create_table succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
