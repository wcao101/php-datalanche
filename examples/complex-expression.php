<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

    function DLComplexExpression($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $expression = new DLExpression();
        $expression->boolAnd(
            array(
               returnNewExpression()->boolOr(
                    array(
                        returnNewExpression()->column('col3')->equals('hello'),
                        returnNewExpression()->column('col3')->equals('world')
                        )),
                returnNewExpression()->column('col1')->equals('0f21b968-cd28-4d8b-9ea6-33dbcd517ec5')
            ));
        $query = new DLQuery();
        $query->select('*')->from('my_table')->where($expression);
        $results = $client->query($query);

         try
        {
            $results = $client->query($query);
        } catch (Exception $e) {
            echo $e."\n";
        }

    }

    function returnNewExpression()
    {
        return new DLExpression();
    }

DLComplexExpression('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);

?>