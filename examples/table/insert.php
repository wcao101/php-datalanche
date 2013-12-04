<?php
//
// Insert rows into the given table. Must have write access for the given database.
//
// equivalent SQL:
// INSERT INTO my_schema.my_table (col1, col2, col3)
//     VALUES
//     ( '0f21b968-cd28-4d8b-9ea6-33dbcd517ec5', '2012-11-13T01:04:33.389Z', 'hello' ),
//     ( '8bf38716-95ef-4a58-9c1b-b7c0f3185746', '2012-07-26T01:09:04.140Z', 'world' ),
//     ( '45db0793-3c99-4e0d-b1d0-43ab875638d3', '2012-11-30T07:10:36.871Z', 'hello world' );
//
require_once(dirname(__FILE__) . '/../Datalanche.php');

try {

    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $q = new DLQuery('my_database');
    $q->insertInto('my_schema.my_table');
    $q->values(array(
        array(
            'col1' => '0f21b968-cd28-4d8b-9ea6-33dbcd517ec5',
            'col2' => '2012-11-13T01:04:33.389Z',
            'col3' => 'hello'
        ),
        array(
            'col1' => '8bf38716-95ef-4a58-9c1b-b7c0f3185746',
            'col2' => '2012-07-26T01:09:04.140Z',
            'col3' => 'world'
        ),
        array(
            'col1' => '45db0793-3c99-4e0d-b1d0-43ab875638d3',
            'col2' => '2012-11-30T07:10:36.871Z',
            'col3' => 'hello world'
        )
    ));

    $result = $client->query($q);

    print_r($result);

} catch (DLException $e) {
    echo $e . "\n";
} catch (Exception $ex) {
    echo $ex . "\n";
}
?>
