<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

try {
    $client = new DLClient('YOUR_API_KEY', 'YOUR_API_SECRET');

    $definition = array(
        'schema_name' => 'my_schema',
        'table_name' => 'my_table',
        'description' => 'the table description goes here',
        'is_private' => true,
        'license' => array(
            'name' => 'public domain',
            'description' => 'this table is public domain',
            'url' => NULL
        ),
        'sources' => array(
            'source1' => array(
                'description' => 'source1 description',
                'url' => 'http://source1.com'
            ),
            'source2' => array(
                'description' => 'source2 description',
                'url' => 'http://source2.com'
            )
        ),
        'columns' => array(
            'col1' => array(
                'data_type' => 'uuid',
                'description' => 'col1 description text',
                'not_null' => true
            ),
            'col2' => array(
                'data_type' => 'text',
                'description' => 'col2 description text',
                'default_value' => NULL,
                'not_null' => false
            ),
            'col3' => array(
                'data_type' => 'integer',
                'description' => 'col3 description text',
                'default_value' => 0,
                'not_null' => true
            )
        ),
        'constraints' => array(
            'primary_key' => 'col1'
        ),
        'indexes' => array(),
        'collaborators' => array(
            'bob' => 'read',
            'slob' => 'read/write',
            'knob' => 'admin'
        )
    );

    $query = new DLQuery();
    $query->createTable($definition);

    $client->query($query);
    echo "createTable succeeded\n";

} catch (DLException $e) {
    echo $e."\n";
} catch (Exception $ex) {
    echo $ex."\n";
}

?>
