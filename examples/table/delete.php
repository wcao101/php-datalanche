<?php
//
// Delete rows from the given table. Must have write access for the given database.
//
// equivalent SQL:
// DELETE FROM my_schema.my_table WHERE col3 = 'hello';
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'));

    //Please find your API credentials here: https://www.datalanche.com/account before use
    $YOUR_API_KEY = $config -> api_key;
    $YOUR_API_SECRET = $config -> api_secret;

    $client = new DLClient($YOUR_API_KEY, $YOUR_API_SECRET);

    $q = new DLQuery('my_database');
    $q->deleteFrom('my_schema.my_table');
    $q->where($q->expr($q->column('col3'), '=', 'hello'));

    $result = $client->query($q);

    print_r($result);

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
