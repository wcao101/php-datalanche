<?php

include "../lib/DLClient.php";

class DropTableExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $query = new DLQuery();
        $query->dropTable('my_table');

        $results = $client->query($query);
        echo "\n----\n";
        var_dump($results);
        echo "\n----\n";
    }
}

$dropTableExample = new DropTableExample('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);

?>