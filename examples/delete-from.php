<?php


class DeleteFromExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $columnComparison = new DLExpression();
        $columnComparison->column('col3')->equals('hello');
        $query = new DLQuery();
        $query->deleteFrom('my_table');
        $query->where($columnComparison);

        $results = $client->query($query);

        echo "----\n";
        echo "delete-from-table\n";
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