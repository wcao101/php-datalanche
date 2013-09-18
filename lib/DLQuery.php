<?php
/*
* Query.php client library: written for Datalanche on august 20th 2013
* property of: Datalanche
* Target Operation: Formulate query for php interface to interact with datalanche
* servers.
*/

class Query
{
    private $_methodType;
    private $_baseUrl;
    private $_parameters;
    /*
    public $test_key;
    public $test_secret;
    */

    public function __construct() {

        $this->_methodType = 'get';
        $this->_baseUrl = '/';

        $this->_parameters = array();

        return $this;
    }

    public function addColumn($object) {

        if( array_key_exists('add_columns', $this->_parameters) === false ) {

            $this->_parameters['add_columns'] = array();
        }

        array_push($this->_parameters['add_columns'], $object);

        return $this;
    }

   
    public function alterColumn($columnName, $object)
    {
        if( array_key_exists('alter_columns', $this->_parameters) === false ) {

            $this->_parameters['alter_column'] = array();
        }

        $this->_parameters['alter_columns'][$columnName] = $object;

        return $this;
    }

    public function alterTable($tableName)
    {
        $this->_methodType = 'post';
        $this->_baseUrl = '/alter_table';
        $this->_parameters['table_name'] = $tableName;

        return $this;
    }

    public function columns($objectArray)
    {
        $this->_parameters['columns'] = $objectArray;

        return $this;
    }

    public function createTable($tableName)
    {
        $this->_methodType = 'post';
        $this->_baseUrl = '/create_table';
        $this->_parameters['table_name'] = $tableName;

        return $this;
    }

    public function deleteFrom($tableName)
    {
        $this->_methodType = 'post';
        $this->_baseUrl = '/delete_from';
        $this->_parameters['table_name'] = $tableName;

        return $this;
    }

    public function description($text)
    {
        $this->_parameters['description'] = $text;

        return $this;
    }

    public function distinct($boolean)
    {
        $this->_parameters['distinct'] = $boolean;

        return $this;
    }

    public function dropColumn($columnName)
    {
        if( array_key_exists('drop_columns', $this->_parameters) === false ) {

            $this->_parameters['drop_columns'] = array();
        }

        array_push($this->_parameters['drop_columns'], $columnName);

        return $this;
    }

    public function dropTable($tableName)
    {
        $this->_methodType = 'del';
        $this->_baseUrl = '/drop_table';
        $this->_parameters['table_name'] = $tableName;

        return $this;
    }

    public function from($table)
    {
        $this->_parameters['from'] = $table;

        return $this;
    }

    public function getTableInfo($tableName)
    {
        $this->_methodType = 'get';
        $this->_baseUrl = '/get_table_info';
        $this->_parameters['table_name'] = $tableName;

        return $this;
    }

    public function getTableList()
    {
        $this->_methodType = 'get';
        $this->_baseUrl = '/get_table_list';

        return $this;
    }
    public function groupBy($columns)
    {
        $this->_parameters['group_by'] = $columns;

        return $this;
    }

    public function insertInto($tableName)
    {
        $this->_methodType = 'post';
        $this->_baseUrl = '/insert_into';
        $this->_parameters['table_name'] = $tableName;

        return $this;
    }

    public function isPrivate($boolean)
    {
        $this->_parameters['is_private'] = $boolean;

        return $this;
    }

    public function license($object)
    {
        $this->_parameters['license'] = $object;

        return $this;
    }

    public function limit($integer)
    {
        $this->_parameters['limit'] = $integer;

        return $this;
    }

    public function offset($integer)
    {
        $this->_parameters['offset'] = $integer;

        return $this;
    }

    public function orderBy($objectArray)
    {
        $this->_parameters['order_by'] = $objectArray;

        return $this;
    }

    public function rename($tableName)
    {
        $this->_parameters['rename'] = $tableName;

        return $this;
    }

    public function select($columns)
    {
        $this->_methodType = 'post';
        $this->_baseUrl = '/select_from';
        $this->_parameters['select'] = $columns;

        return $this;
    }

    public function set($map)
    {
        $this->_parameters['set'] = $map;

        return $this;
    }

    public function sources($objectArray)
    {
        $this->_parameters['sources'] = $objectArray;

        return $this;
    }

    public function total($boolean)
    {
        $this->_parameters['total'] = $boolean;

        return $this;
    }

    public function update($tableName)
    {
        $this->_methodType = 'post';
        $this->_baseUrl = '/update';
        $this->_parameters['table_name'] = $tableName;

        return $this;
    }

    public function values($rows)
    {
        $this->_parameters['values'] = $rows;

        return $this;
    }

    public function where($filter)
    {
        if($filter instanceof DLExpression)
        {
            $this->_parameters['where'] = $filter->json();
        } else {
            $this->_parameters['where'] = $filter;        
        }

        return $this;
    }

    public function getMethodType()
    {
        return $this->_methodType;
    }

    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    public function getParameters()
    {
        return $this->_parameters;
    }
    public function setMethod($method)
    {
        $this->_methodType = $method;
    }

    public function setBaseUrl($url)
    {
        $this->_baseUrl = $url;
    }
}
?>