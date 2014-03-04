<?php
//
// Drop the given index. Must have admin access for the given database.
//
// equivalent SQL:
// DROP INDEX my_schema.my_index CASCADE;
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'));

    //Please find your API credentials here: https://www.datalanche.com/account before use
    $YOUR_API_KEY = $config -> api_key;
    $YOUR_API_SECRET = $config -> api_secret;

    $client = new DLClient($YOUR_API_KEY, $YOUR_API_SECRET);
    
    $q = new DLQuery('my_database');
    $q->dropIndex('my_schema.my_index');
    $q->cascade(true);

    $client->query($q);

    echo "drop_index succeeded!\n";

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
