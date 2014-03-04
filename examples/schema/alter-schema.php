<?php
//
// Alter the given schema's properties. Must have admin access for the given database.
//
// equivalent SQL:
// ALTER SCHEMA my_schema RENAME TO my_new_schema;
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'));

    //Please find your API credentials here: https://www.datalanche.com/account before use
    $YOUR_API_KEY = $config -> api_key;
    $YOUR_API_SECRET = $config -> api_secret;

    $client = new DLClient($YOUR_API_KEY, $YOUR_API_SECRET);
    $q = new DLQuery('my_database');
    $q->alterSchema('my_schema');
    $q->renameTo('my_new_schema');
    $q->description('my_new_schema description text');

    $client->query($q);

    echo "alter_schema succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
    exit(1);
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
