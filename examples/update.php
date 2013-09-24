<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

try {
    /**
    * @uses DLClient Add your API key and API secret
    */
    $apiKey = 'your_api_key';
    $apiSecret = 'your_api_secret';
    $client = new DLClient($apiSecret, $apiKey);

    $expression = new DLExpression();
    $expression->column('col3')->equals('hello');

    /**
    * @uses DLQuery::update Only $query->update() is required
    *
    * $query->where() is optional however, all rows in the table
    * will be updated in the specified table if the where clause 
    * is missing.
    */
    $query = new DLQuery();
    $query->update('my_table');
    $query->set(array('col3' => 'hello world'));
    $query->where($expression);

    $results = $client->query($query);
    echo "Operation Update-Table Successful\n";

} catch (DLException $e) {
    echo $e."\n";
} catch (Exception $ex) {
    echo $ex."\n";
}

?>