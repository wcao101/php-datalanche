<?php
//
// Create the given schema. Must have admin access for the given database.
//
// equivalent SQL:
// CREATE SCHEMA my_schema;
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'));

    //Please find your API credentials here: https://www.datalanche.com/account before use
    $YOUR_API_KEY = $config -> api_key;
    $YOUR_API_SECRET = $config -> api_secret;

    $client = new DLClient($YOUR_API_KEY, $YOUR_API_SECRET);
    $q = new DLQuery('my_database');
    $q->createSchema('my_schema');
    $q->description('my_schema description text');

    $client->query($q);

    echo "create_schema succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
