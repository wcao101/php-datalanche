<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

try {
    /**
    * @uses DLClient add your API secret and API key
    */
    $apiKey = 'your_api_key';
    $apiSecret = 'your_api_secret';
    $client = new DLClient($apiSecret, $apiKey);

    $columnComparison = new DLExpression();
    $columnComparison->column('col3')->equals('hello');

    /**
    * @uses DLQuery::deleteFrom() Only $query->deleteFrom() is required
    *
    * $query->where() is optional however, all rows will be deleted from
    * the specified table if the where clause is missing.
    */
    $query = new DLQuery();
    $query->deleteFrom('my_table');
    $query->where($columnComparison);

    $results = $client->query($query);

    echo "Operation Delete-From succesful.\n";

} catch (DLException $e) {
    echo $e."\n";
} catch (Exception $ex) {
    echo $ex."\n";
}


?>