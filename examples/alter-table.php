<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

try {
    /**
    * @uses DLClient add your API key and API secret
    */
    $apiKey = 'your_api_key';
    $apiSecret = 'your_api_secret';
    $client = new DLClient($apiSecret, $apiKey);

    /**
    * @uses DLQuery::alterTable() only $query->alterTable() is needed
    *
    * All other arguments within this function are optional.
    * If present, these value will overide current values.
    * However add/drop/alter columns are broken up into
    * individual functions and require explicit statement.
    * NOTE: Dropping or altering columns of an existing table
    * may result in loss of data.
    */
    $query = new DLQuery();
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
    $query->alterColumn('col1', array(
        //will only alter col1's data type
        'data_type'=>'string'
        ));

    $client->query($query);

    echo "Operation Alter-Table Successful.\n";

} catch (DLException $e) {
    echo $e."\n";
} catch (Exception $ex) {
    echo $ex."\n";
}
    


?>