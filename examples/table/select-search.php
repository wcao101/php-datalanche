<?php
//
// Search the table and retrieve the rows. Must have read access for the given database.
//
// equivalent SQL:
// SELECT * FROM my_schema.my_table WHERE SEARCH 'hello world'
//
// NOTE: Search clause is sent to ElasticSearch. The search
// results are used as a filter when executing the SQL query.
//
require_once(dirname(__FILE__) . '/../../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->selectAll()->from('my_schema.my_table')->search('hello world');

    $result = $client->query($q);

    print_r($result);

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
