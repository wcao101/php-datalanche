<?php

include "../lib/DLClient.php";

class SelectFromExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $expression = new DLExpression();
        $expression->column('col3')->contains('hello');
        $query = new DLQuery();
        $query->select(array('col1', 'col2'));
        $query->from('my_table');
        $query->where($expression);
        $query->orderBy(
            array(
                array( 'col1' => '$asc' ),
                array( 'col2' => '$desc' )
                ));
        $query->offset(0);
        $query->limit(10);
        $query->total(true);

        $results = $client->query($query);

        echo "\n----\n";
        var_dump($results);
        echo "\n----\n";
    }
}

$selectFromExample = new SelectFromExample('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);
?>