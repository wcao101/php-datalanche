<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

try{
    /**
    * @uses DLClient Add your API secret and API key
    */
    $apiKey = 'your_api_key';
    $apiSecret = 'your_api_secret';
    $client = new DLClient($apiSecret, $apiKey);

    $query = new DLQuery();
    $query->getTableList();

    $results = $client->query($query);
} catch (DLException $e) {
    echo $e."\n";
} catch (Exception $ex) {
    echo $ex."\n";
}

?>