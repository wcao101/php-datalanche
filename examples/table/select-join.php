<?php
//
// Join multiple tables and retrieve the rows. Must have read access for the given database.
//
// equivalent SQL:
// SELECT * FROM t1
//     JOIN t2 ON t1.c1 = t2.c1
//     JOIN t3 ON t1.c1 = t3.c1
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'));

    //Please find your API credentials here: https://www.datalanche.com/account before use
    $YOUR_API_KEY = $config -> api_key;
    $YOUR_API_SECRET = $config -> api_secret;

    $client = new DLClient($YOUR_API_KEY, $YOUR_API_SECRET);

    $q = new DLQuery('my_database');
    $q->selectAll();
    $q->from($q->expr(
        $q->table('t1'),
        '$join', $q->table('t2'), '$on', $q->column('t1.col1'), '=', $q->column('t2.col1'),
        '$join', $q->table('t3'), '$on', $q->column('t1.col1'), '=', $q->column('t3.col1')
    ));

    $result = $client->query($q);

    print_r($result);

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
