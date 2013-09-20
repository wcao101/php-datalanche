<?php

class SelectFromExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
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

        $results = $client->query($query);

         echo "----\n";
         echo "select-from:\n";
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