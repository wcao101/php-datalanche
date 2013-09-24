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
    $expression->column('col3')->contains('hello');

    $query = new DLQuery();
    $query->select(array('col1', 'col2')); //if you want all columns use $query->select('*')
    $query->from('my_table');
    $query->where($expression);
    $query->orderBy(array(
            array( 'col1' => '$asc' ),
            array( 'col2' => '$desc' )
        ));
    $query->offset(0);
    $query->limit(10);
    $query->total(true);

    $results = $client->query($query);
    print_r($results['data']);

} catch (DLException $e) {
    echo $e."\n";
} catch (Exception $ex) {
    echo $ex."\n";
}


?>