<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

try {
    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery();
    $q->select(array('col1', 'col2'));
    $q->from('my_schema.my_table');
    $q->where($q->expr($q->column('col3'), '$like', '%hello%'));
    $q->orderBy(array(
        $q->expr($q->column('col1'), '$asc'),
        $q->expr($q->column('col2'), '$desc')
    ));
    $q->offset(0);
    $q->limit(10);
    $q->total(true);

    $result = $client->query($q);
    print_r($result['data']);

} catch (DLException $e) {
    echo $e."\n";
} catch (Exception $ex) {
    echo $ex."\n";
}


?>
