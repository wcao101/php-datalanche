<?php
//
// Select rows from the given table. Must have read access for the given database.
//
// equivalent SQL:
// SELECT DISTINCT col1, col2
//     FROM my_schema.my_table
//     WHERE col3 LIKE '%hello%'
//     ORDER BY col1 ASC, col2 DESC
//     OFFSET 0 LIMIT 10;
//
require_once(dirname(__FILE__) . '/../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->select(array('col1', 'col2'));
    $q->distinct(true);
    $q->from('my_schema.my_table');
    $q->where($q->expr($q->column('col3'), '$like', '%hello%'));
    $q->orderBy(array(
        $q->expr($q->column('col1'), '$asc'),
        $q->expr($q->column('col2'), '$desc')
    ));
    $q->offset(0);
    $q->limit(10);

    $result = $client->query($q);

    print_r($result);

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>