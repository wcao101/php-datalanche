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

    $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'));

    //Please find your API credentials here: https://www.datalanche.com/account before use
    $YOUR_API_KEY = $config -> api_key;
    $YOUR_API_SECRET = $config -> api_secret;

    $client = new DLClient($YOUR_API_KEY, $YOUR_API_SECRET);

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
                'name' => 'timestamptz'
            ),
            'description' => 'col2 description text',
            'default_value' => NULL,
            'not_null' => false
        ),
        'col3' => array(
            'data_type' => array(
                'name' => 'text'
            ),
            'description' => 'col3 description text',
            'default_value' => 'default_text',
            'not_null' => true
        ),
        'col4' => array(
            'data_type' => array(
                'name' => 'varchar',
                'args' => array(50)
            ),
            'description' => 'col4 description text'
        )

    ));

    $client->query($q);

    echo "create_table succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
    exit(1);
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
