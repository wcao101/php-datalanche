<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

try {
    /**
    * @uses DLClient add your API secret and API key
    */
    $apiKey = 'your_api_key';
    $apiSecret = 'your_api_secret';
    $client = new DLClient($apiSecret, $apiKey);

    /**
    * @uses DLQuery::createTable() Only $query->createTable() is required
    *
    * All other parameters are optional and the server will set defaults for
    * arguments that are not given.
    */
    $query = new DLQuery();
    $query->createTable('my_table');
    $query->description('my_table description text');
    $query->isPrivate(true);

    $query->license(array(
        'name' => 'license_name',
        'url' => 'http://license.com',
        'description' => 'license description text'
        ));

    $query->sources(array(
            array(
                'name' => 'source1',
                'url' => 'http://source1.com',
                'description' => 'source1 description text'
                ),
            array(
                'name' => 'source2',
                'url' => 'http://source2.com',
                'description' => 'source2 description text'
                )
            ));

    $query->columns(array(
            array(
                'name' => 'col1',
                'data_type' => 'uuid',
                'description' => 'col1 description text'
                ),
                array(
                    'name' => 'col2',
                    'data_type' => 'timestamp',
                    'description' => 'col2 description text'
                    ),
                array(
                'name' => 'col3',
                'data_type' => 'string',
                'description' => 'col3 description text'
                )
            ));


    $results = $client->query($query);

    echo "Operation Create-Table Successful.\n";

} catch (DLException $e) {
    echo $e."\n";
} catch(Exception $ex) {
    echo $ex."\n";
}

?>