<?php

class DLQuery
{
    private $_url;
    private $_params;

    public function __construct()
    {
        $this->_url = '/';
        $this->_params = array();

        return $this; // method chaining
    }

    //
    // ALTER TABLE
    //

    public function alterTable($tableName)
    {
        $this->_url = '/alter_table';
        $this->_params['table_name'] = $tableName;

        return $this; // method chaining
    }

    public function addCollaborator($username, $permission)
    {
        if (array_key_exists('add_collaborators', $this->_params) === false) {
            $this->_params['add_collaborators'] = new stdClass();
        }
        $this->_params['add_collaborators']->$username = $permission;

        return $this; // method chaining
    }

    public function addColumn($columnName, $attributes)
    {
        if (array_key_exists('add_columns', $this->_params) === false) {
            $this->_params['add_columns'] = new stdClass();
        }
        $this->_params['add_columns']->$columnName = $attributes;

        return $this; // method chaining
    }

    public function addSource($sourceName, $attributes)
    {
        if (array_key_exists('add_sources', $this->_params) === false) {
            $this->_params['add_sources'] = new stdClass();
        }
        $this->_params['add_sources']->$sourceName = $attributes;

        return $this; // method chaining
    }

    public function alterCollaborator($username, $permission)
    {
        if (array_key_exists('alter_collaborators', $this->_params) === false) {
            $this->_params['alter_collaborators'] = new stdClass();
        }
        $this->_params['alter_collaborators']->$username = $permission;

        return $this; // method chaining
    }

    public function alterColumn($columnName, $attributes)
    {
        if (array_key_exists('alter_columns', $this->_params) === false) {
            $this->_params['alter_columns'] = new stdClass();
        }
        $this->_params['alter_columns']->$columnName = $attributes;

        return $this; // method chaining
    }

    public function alterSource($sourceName, $attributes)
    {
        if (array_key_exists('alter_sources', $this->_params) === false) {
            $this->_params['alter_sources'] = new stdClass();
        }
        $this->_params['alter_sources']->$sourceName = $attributes;

        return $this; // method chaining
    }

    public function description($text)
    {
        $this->_params['description'] = $text;

        return $this; // method chaining
    }

    public function dropCollaborator($username)
    {
        if (array_key_exists('drop_collaborators', $this->_params) === false) {
            $this->_params['drop_collaborators'] = array();
        }
        array_push($this->_params['drop_collaborators'], $username);

        return $this; // method chaining
    }

    public function dropColumn($columnName)
    {
        if (array_key_exists('drop_columns', $this->_params) === false) {
            $this->_params['drop_columns'] = array();
        }
        array_push($this->_params['drop_columns'], $columnName);

        return $this; // method chaining
    }

    public function dropSource($sourceName)
    {
        if (array_key_exists('drop_sources', $this->_params) === false) {
            $this->_params['drop_sources'] = array();
        }
        array_push($this->_params['drop_sources'], $sourceName);

        return $this; // method chaining
    }

    public function isPrivate($boolean)
    {
        $this->_params['is_private'] = $boolean;

        return $this; // method chaining
    }

    public function license($attributes)
    {
        $this->_params['license'] = $attributes;

        return $this; // method chaining
    }

    public function renameColumn($columnName, $newName)
    {
        if (array_key_exists('rename_columns', $this->_params) === false) {
            $this->_params['rename_columns'] = new stdClass();
        }
        $this->_params['rename_columns']->$columnName = $newName;

        return $this; // method chaining
    }

    public function renameSource($sourceName, $newName)
    {
        if (array_key_exists('rename_sources', $this->_params) === false) {
            $this->_params['rename_sources'] = new stdClass();
        }
        $this->_params['rename_sources']->$sourceName = $newName;

        return $this; // method chaining
    }

    public function renameTo($tableName)
    {
        $this->_params['rename_to'] = $tableName;

        return $this; // method chaining
    }

    public function setSchema($schemaName)
    {
        $this->_params['set_schema'] = $schemaName;

        return $this; // method chaining
    }

    //
    // CREATE TABLE
    //

    public function createTable($definition)
    {
        $this->_url = '/create_table';
        $this->_params = $definition;

        return $this; // method chaining
    }

    //
    // DELETE
    //

    public function deleteFrom($tableName)
    {
        $this->_url = '/delete';
        $this->_params['table_name'] = $tableName;

        return $this; // method chaining
    }

    // where() defined below

    //
    // DROP TABLE
    //

    public function dropTable($tableName)
    {
        $this->_url = '/drop_table';
        $this->_params['table_name'] = $tableName;

        return $this; // method chaining
    }

    //
    // GET TABLE INFO
    //

    public function getTableInfo($tableName)
    {
        $this->_url = '/get_table_info';
        $this->_params['table_name'] = $tableName;

        return $this; // method chaining
    }

    //
    // GET TABLE LIST
    //

    public function getTableList()
    {
        $this->_url = '/get_table_list';

        return $this; // method chaining
    }

    //
    // INSERT
    //

    public function insertInto($tableName)
    {
        $this->_url = '/insert';
        $this->_params['table_name'] = $tableName;

        return $this; // method chaining
    }

    public function values($rows)
    {
        $this->_params['values'] = $rows;

        return $this; // method chaining
    }

    //
    // SELECT
    //

    public function select($columns)
    {
        $this->_url = '/select';
        $this->_params['select'] = $columns;

        return $this; // method chaining
    }

    public function distinct($boolean)
    {
        $this->_params['distinct'] = $boolean;

        return $this; // method chaining
    }

    public function from($tables)
    {
        $this->_params['from'] = $tables;

        return $this; // method chaining
    }

    public function groupBy($columns)
    {
        $this->_params['group_by'] = $columns;

        return $this; // method chaining
    }

    public function limit($integer)
    {
        $this->_params['limit'] = $integer;

        return $this; // method chaining
    }

    public function offset($integer)
    {
        $this->_params['offset'] = $integer;

        return $this; // method chaining
    }

    public function orderBy($exprArray)
    {
        $this->_params['order_by'] = $exprArray;

        return $this; // method chaining
    }

    public function total($boolean)
    {
        $this->_params['total'] = $boolean;

        return $this; // method chaining
    }

    // where() defined below

    //
    // UPDATE
    //

    public function update($tableName)
    {
        $this->_url = '/update';
        $this->_params['table_name'] = $tableName;

        return $this; // method chaining
    }

    public function set($map)
    {
        $this->_params['set'] = $map;

        return $this; // method chaining
    }

    // where() defined below

    //
    // COMMON CLAUSES
    //

    public function where($expression)
    {
        $this->_params['where'] = $expression;        

        return $this; // method chaining
    }

    //
    // HELPERS
    //

    public function getUrl()
    {
        return $this->_url;
    }

    public function getParams()
    {
        return $this->_params;
    }

    //
    // EXPRESSIONS
    //

    public function column($columnName)
    {
        return array('$column' => $columnName);
    }

    //
    // usage examples
    //
    // q->expr(2, "$+", 2)
    // q->expr("$~", 2)
    // q->expr(2, "$!")
    // q->expr(q->column("c1"), "$like", "%abc%")
    // q->expr(q->column("c1"), "$not", "$in", [1, 2, 3, 4])
    // q->expr(q->column("c1"), "$=", 1, "$and", q->column("c2"), "$=", 2)
    //
    public function expr()
    {
        return array('$expr' => func_get_args());
    }
}

?>
