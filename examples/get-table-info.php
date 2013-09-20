<?php

include "../lib/client.php";

class GetTableInfoExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new Client($secret, $key, $host, $port, $ssl);
        $query = new Query();
        $query->getTableInfo('my_table');
        $test = $query->getParameters;


        $results = $client->query($query);

        echo "\n----\n";
        var_dump($results);
        echo "\n----\n";
    }
}

$getTableInfoExample = new GetTableInfoExample('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);
?>