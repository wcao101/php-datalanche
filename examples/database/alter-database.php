<?php
//
// Alter the given database's properties. Must have admin access for the database.
//
// equivalent SQL:
// ALTER DATABASE my_database RENAME TO my_new_database;
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'));

    //Please find your API credentials here: https://www.datalanche.com/account before use
    $YOUR_API_KEY = $config -> api_key;
    $YOUR_API_SECRET = $config -> api_secret;

    $client = new DLClient($YOUR_API_KEY, $YOUR_API_SECRET);

    $q = new DLQuery();
    $q->alterDatabase('my_database');
    $q->renameTo('my_new_database');
    $q->description('my_new_database description text');

    $client->query($q);

    echo "alter_database succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
    exit(1);
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
