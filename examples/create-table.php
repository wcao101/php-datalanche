<?php
include '../lib/DLClient.php';

class CreateTableExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $result = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $query = new DLQuery();
        $query->createTable('my_table');
        $query->description('my_table description text');
        $query->isPrivate(true);
        $query->license(
                array(
                    'name' => 'license_name',
                    'url' => 'http://license.com',
                    'description' => 'license description text'
                    ));
        $query->sources(
                array(
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
        $query->columns(
                array(
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
            echo "\n----\n";
            var_dump($results);
            echo "\n----\n";
    }
}

$createTableExample = new CreateTableExample('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);

?>