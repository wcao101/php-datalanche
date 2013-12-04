<?php
//
// Update rows in the given table. Must have write access for the given database.
//
// equivalent SQL:
// UPDATE my_schema.my_table SET col3 = 'hello world' WHERE col3 = 'hello';
//
require_once(dirname(__FILE__) . '/../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->update('my_schema.my_table');
    $q->set(array(
        'col3' => 'hello world'
    ));
    $q->where($q->expr($q->column('col3'), '=', 'hello'));

    $result = $client->query($q);

    print_r($result);

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
