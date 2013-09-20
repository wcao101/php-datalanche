<?php


class DropTableExample
{
    public function __construct($secret, $key, $host, $port, $ssl, $tableName)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $query = new DLQuery();
        $query->dropTable($tableName);

        $results = $client->query($query);
        echo "----\n";
        echo "drop-table:\n";
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