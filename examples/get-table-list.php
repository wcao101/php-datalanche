<?php

class GetTableListExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $query = new DLQuery();
        $query->getTableList();

        $results = $client->query($query);

        echo "----\n";
        echo "get table list:\n";
            echo "code: ".$results['response']['headers']['http_code']."\n";
            if($results['response']['headers']['http_code'] === 200)
            {
                echo "!! PASS !!\n";
                echo "----\n";
                return true;
            } else {
                echo "!! FAIL !!\n";
                echo "----\n";
                return false;
            }
    }
}



?>