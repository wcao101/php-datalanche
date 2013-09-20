<?php

include "../lib/client.php";

class DeleteFromExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new Client($secret, $key, $host, $port, $ssl);
        $columnComparison = new DLExpression();
        $columnComparison->column('col3')->equals('hello');
        $query = new Query();
        $query->deleteFrom('my_table');
        $query->where($columnComparison);

        $results = $client->query($query);

        echo "\n----\n";
        var_dump($results);
        echo "\n----\n";
    }
}

$deleteFromExample = new DeleteFromExample('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);
?>