<?php
//
// Show details of given database. Must have read access for the database.
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'));

    //Please find your API credentials here: https://www.datalanche.com/account before use
    define('YOUR_API_KEY', $config->api_key);
    define('YOUR_API_SECRET', $config->api_secret);

    $client = new DLClient(YOUR_API_KEY, YOUR_API_SECRET);

    $q = new DLQuery();
    $q->describeDatabase('my_database');

    $result = $client->query($q);

    print_r($result);

} catch (DLException $e) {
    echo $e . "\n";
    exit(1);
} catch (Exception $ex) {
    echo $ex . "\n";
    exit(1);
}
?>
