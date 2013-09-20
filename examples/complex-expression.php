<?php

include "../lib/client.php";

class ComplexExpressionExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new Client($secret, $key, $host, $port, $ssl);
        $expression = new DLExpression();
        $expression->boolAnd(
            array(
               $this->returnNewExpression()->boolOr(
                    array(
                        $this->returnNewExpression()->column('col3')->equals('hello'),
                        $this->returnNewExpression()->column('col3')->equals('world')
                        )),
                $this->returnNewExpression()->column('col1')->equals('0f21b968-cd28-4d8b-9ea6-33dbcd517ec5')
            ));
        $query = new Query();
        $query->select('*')->from('my_table')->where($expression);
        $results = $client->query($query);

        echo "\n----\n";
        var_dump($results);
        echo "\n----\n";
    }

    private function returnNewExpression()
    {
        return new DLExpression();
    }
}

$complexExpressionExample = new ComplexExpressionExample('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);
?>