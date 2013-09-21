<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');
$apiKey = 'your_api_key';
$apiSecret = 'your_api_secret';
$host = 'your_host';
$port = 'wanted_port';
$ssl = 'verify_ssl';

   function DLSelectFrom($secret, $key, $host, $port, $ssl)
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

        try
        {
            $results = $client->query($query);
        } catch (Exception $e) {
            echo $e."\n";
        }
    }
DLSelectFrom($apiKey,$apiSecret, $host, $port, $ssl);

?>