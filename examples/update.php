<?php

include "../lib/DLClient.php";

class UpdateExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $expression = new DLExpression();
        $expression->column('col3')->equals('hello');

        $query = new DLQuery();
        $query->update('my_table');
        $query->set(array('col3' => 'hello world'));
        $query->where($expression);

        $results = $client->query($query);

        echo "\n----\n";
        var_dump($results);
        echo "\n----\n";
    }
}

$updateExample = new UpdateExample('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);
?>