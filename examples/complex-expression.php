<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

/**
* @return object DLExpression Cleanly refrence a new instance of DLExpression
*/
function returnNewExpression()
{
    return new DLExpression();
}

try {
    /**
    * @uses DLClient add your API key and API secret
    */
    $apiKey = 'your_api_key';
    $apiSecret = 'your_api_secret';
    $client = new DLClient($apiSecret, $apiKey);

    $expression = new DLExpression();
    $expression->boolAnd(
    array(
        returnNewExpression()->boolOr(
            array(
                    returnNewExpression()->column('col3')->equals('hello'),
                    returnNewExpression()->column('col3')->equals('world')
                )),
                returnNewExpression()->column('col1')->
                    equals('0f21b968-cd28-4d8b-9ea6-33dbcd517ec5')
        ));

    $query = new DLQuery();
    $query->select('*')->from('my_table')->where($expression);

    $results = $client->query($query);

} catch (DLException $e) {
    echo $e."\n";
} catch (Exception $ex) {
    echo $e."\n";
}
?>