<?php

include "../lib/client.php";

class AlterTableExample
{
    public function __construct($secret, $key, $host, $port, $ssl)
    {
        $client = new Client($secret, $key, $host, $port, $ssl);
        $query = new Query();
        $results = null;
        $query->alterTable('my_table');
        $query->rename('my_new_table');
        $query->description('my_new_table description text');
        $query->isPrivate(false);
        $query->license(array(
            'name' => 'new license name',
            'url' => 'http://new_license_name.com',
            'description' => 'new license descritpion text'
            ));
        $query->sources(array(
            array(
                'name' => 'new source1',
                'url' => 'http://new_source1.com',
                'description' => 'new source1 description text'
                ),
            array(
                'name' => 'new source2',
                'url' => 'http://new_source2.com',
                'description' => 'new source2 description text'
                )
            ));
        $query->addColumn(array(
            'name' => 'new_col',
            'data_type' => 'int32',
            'description' => 'new_col description text'
            ));
        $query->dropColumn('col2');
        $query->dropColumn('col3');
        $query->alterColumn('col1', array('data_type'=>'string'));
        $results = $client->query($query);
        echo "\n----\n";
        var_dump($results);
        echo "\n----\n";
    }

}

$alterTableExample = new AlterTableExample('VCBA1hLyS2mYdrL6kO/iKQ==','7zNN1Pl9SQ6lNZwYe9mtQw==', 'localhost', 4001, false);
?>