<?php
//
// Create an index on the given table. Must have admin access for the given database.
//
// equivalent SQL:
// CREATE UNIQUE INDEX my_index ON my_schema.my_table USING btree (col1, col2);
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'));

    //Please find your API credentials here: https://www.datalanche.com/account before use
    $YOUR_API_KEY = $config -> api_key;
    $YOUR_API_SECRET = $config -> api_secret;

    $client = new DLClient($YOUR_API_KEY, $YOUR_API_SECRET);

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
    exit(1);
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
