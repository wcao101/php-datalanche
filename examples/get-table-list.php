<?php

include "../lib/client.php";

class GetTableListExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new Client($secret, $key, $host, $port, $ssl);
        $query = new Query();
        $query->getTableList();

        $results = $client->query($query);

        echo "\n----\n";
        var_dump($results);
        echo "\n----\n";
    }
}

$getTableListExample = new GetTableListExample('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);
?>