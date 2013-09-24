<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

try {
    /**
    * @uses DLClient Add your API key and API secret
    */
    $apiKey = 'your_api_key';
    $apiSecret = 'your_api_secret';
    $client = new DLClient($apiSecret, $apiKey);

    $query = new DLQuery();
    $query->insertInto('my_table');
    $query->values(
        array(
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

    $results = $client->query($query);
    echo "Operation Insert-Into successful\n";

} catch (DLException $e) {
    echo $e."\n";
} catch (Exception $ex) {
    echo $ex."\n";
}


?>