<?php


class UpdateExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $results = null;
        $client = new DLClient($secret, $key, $host, $port, $ssl);
        $expression = new DLExpression();
        $expression->column('col3')->equals('hello');

        $query = new DLQuery();
        $query->update('my_table');
        $query->set(array('col3' => 'hello world'));
        $query->where($expression);

        $results = $client->query($query);

        echo "----\n";
        echo "update-table:\n";
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