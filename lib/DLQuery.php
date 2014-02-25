<?php

class DLQuery
{
    private $_params;

    public function __construct($databaseName = NULL)
    {
        $this->_params = array();
        if ($databaseName != NULL) {
            $this->_params['database'] = $databaseName;
        }
        return $this; // method chaining
    }

    //
    // HELPERS
    //

    public function getParams()
    {
        return $this->_params;
    }

    //
    // COMMON
    //

    public function cascade($boolean)
    {
        $this->_params['cascade'] = $boolean;
        return $this; // method chaining
    }

    public function columns($columns)
    {
        $this->_params['columns'] = $columns;
        return $this; // method chaining
    }

    public function description($text)
    {
        $this->_params['description'] = $text;
        return $this; // method chaining
    }

    public function renameTo($tableName)
    {
        $this->_params['rename_to'] = $tableName;
        return $this; // method chaining
    }

    public function where($expression)
    {
        $this->_params['where'] = $expression;        
        return $this; // method chaining
    }

    //
    // EXPRESSIONS
    //

    //
    // usage examples
    //
    // q->expr(2, "+", 2)
    // q->expr("~", 2)
    // q->expr(2, "!")
    // q->expr(q->column("c1"), "$like", "%abc%")
    // q->expr(q->column("c1"), "$not", "$in", [1, 2, 3, 4])
    // q->expr(q->column("c1"), "=", 1, "$and", q->column("c2"), "=", 2)
    //
    public function expr()
    {
        return array('$expr' => func_get_args());
    }

    public function alias($aliasName)
    {
        return array('$alias' => $aliasName);
    }

    public function column($columnName)
    {
        return array('$column' => $columnName);
    }

    public function literal($value)
    {
        return array('$literal' => $value);
    }

    public function table($tableName)
    {
        return array('$table' => $tableName);
    }

    //
    // FUNCTIONS
    //

    //
    // usage examples
    //
    // q->func("$count", "*")
    // q->func("$sum", q->column("c1"))
    //
    public function func()
    {
        return array('$function' => func_get_args());
    }

    public function avg()
    {
        $args = array('$avg');
        $args = array_merge($args, func_get_args());
        return array('$function' => $args);
    }

    public function count()
    {
        $args = array('$count');
        $args = array_merge($args, func_get_args());
        return array('$function' => $args);
    }

    public function max()
    {
        $args = array('$max');
        $args = array_merge($args, func_get_args());
        return array('$function' => $args);
    }

    public function min()
    {
        $args = array('$min');
        $args = array_merge($args, func_get_args());
        return array('$function' => $args);
    }

    public function sum()
    {
        $args = array('$sum');
        $args = array_merge($args, func_get_args());
        return array('$function' => $args);
    }

    //
    // ALTER DATABASE
    //

    public function alterDatabase($databaseName)
    {
        $this->_params['alter_database'] = $databaseName;
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

    public function alterCollaborator($username, $permission)
    {
        if (array_key_exists('alter_collaborators', $this->_params) === false) {
            $this->_params['alter_collaborators'] = new stdClass();
        }
        $this->_params['alter_collaborators']->$username = $permission;

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

    public function isPrivate($boolean)
    {
        $this->_params['is_private'] = $boolean;
        return $this; // method chaining
    }

    public function maxSizeGB($integer)
    {
        $this->_params['max_size_gb'] = $integer;
        return $this; // method chaining
    }

    //
    // ALTER INDEX
    //

    public function alterIndex($indexName)
    {
        $this->_params['alter_index'] = $indexName;
        return $this; // method chaining
    }

    //
    // ALTER SCHEMA
    //

    public function alterSchema($schemaName)
    {
        $this->_params['alter_schema'] = $schemaName;
        return $this; // method chaining
    }

    //
    // ALTER TABLE
    //

    public function alterTable($tableName)
    {
        $this->_params['alter_table'] = $tableName;
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

    // TODO: addConstraint

    public function alterColumn($columnName, $attributes)
    {
        if (array_key_exists('alter_columns', $this->_params) === false) {
            $this->_params['alter_columns'] = new stdClass();
        }
        $this->_params['alter_columns']->$columnName = $attributes;

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

    // TODO: dropConstraint

    public function renameColumn($columnName, $newName)
    {
        if (array_key_exists('rename_columns', $this->_params) === false) {
            $this->_params['rename_columns'] = new stdClass();
        }
        $this->_params['rename_columns']->$columnName = $newName;

        return $this; // method chaining
    }

    // TODO: dropConstraint

    public function setSchema($schemaName)
    {
        $this->_params['set_schema'] = $schemaName;
        return $this; // method chaining
    }

    //
    // CREATE INDEX
    //

    public function createIndex($indexName)
    {
        $this->_params['create_index'] = $indexName;
        return $this; // method chaining
    }

    public function onTable($tableName)
    {
        $this->_params['on_table'] = $tableName;
        return $this; // method chaining
    }

    public function unique($boolean)
    {
        $this->_params['unique'] = $boolean;
        return $this; // method chaining
    }

    public function usingMethod($text)
    {
        $this->_params['using_method'] = $text;
        return $this; // method chaining
    }

    //
    // CREATE SCHEMA
    //

    public function createSchema($schemaName)
    {
        $this->_params['create_schema'] = $schemaName;
        return $this; // method chaining
    }

    //
    // CREATE TABLE
    //

    public function createTable($tableName)
    {
        $this->_params['create_table'] = $tableName;
        return $this; // method chaining
    }

    // TODO: constraints

    //
    // DELETE
    //

    public function deleteFrom($tableName)
    {
        $this->_params['delete_from'] = $tableName;
        return $this; // method chaining
    }

    //
    // DESCRIBE DATABASE
    //

    public function describeDatabase($databaseName)
    {
        $this->_params['describe_database'] = $databaseName;
        return $this; // method chaining
    }

    //
    // DESCRIBE SCHEMA
    //

    public function describeSchema($schemaName)
    {
        $this->_params['describe_schema'] = $schemaName;
        return $this; // method chaining
    }

    //
    // DESCRIBE TABLE
    //

    public function describeTable($tableName)
    {
        $this->_params['describe_table'] = $tableName;
        return $this; // method chaining
    }

    //
    // DROP INDEX
    //

    public function dropIndex($indexName)
    {
        $this->_params['drop_index'] = $indexName;
        return $this; // method chaining
    }

    //
    // DROP SCHEMA
    //

    public function dropSchema($schemaName)
    {
        $this->_params['drop_schema'] = $schemaName;
        return $this; // method chaining
    }

    //
    // DROP TABLE
    //

    public function dropTable($tableName)
    {
        $this->_params['drop_table'] = $tableName;
        return $this; // method chaining
    }

    //
    // INSERT
    //

    public function insertInto($tableName)
    {
        $this->_params['insert_into'] = $tableName;
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
        if ($columns === '*') {
            throw new Exception('please use selectAll() instead of select("*")');
        }

        $this->_params['select'] = $columns;
        return $this; // method chaining
    }

    public function selectAll()
    {
        $this->_params['select'] = true;
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

    public function having($expression)
    {
        $this->_params['having'] = $expression;
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

    public function search($queryText)
    {
        $this->_params['search'] = $queryText;
        return $this; // method chaining
    }

    //
    // SHOW DATABASES
    //

    public function showDatabases()
    {
        $this->_params['show_databases'] = true;
        return $this; // method chaining
    }

    //
    // SHOW SCHEMAS
    //

    public function showSchemas()
    {
        $this->_params['show_schemas'] = true;
        return $this; // method chaining
    }

    //
    // SHOW TABLES
    //

    public function showTables()
    {
        $this->_params['show_tables'] = true;
        return $this; // method chaining
    }

    //
    // UPDATE
    //

    public function update($tableName)
    {
        $this->_params['update'] = $tableName;
        return $this; // method chaining
    }

    public function set($map)
    {
        $this->_params['set'] = $map;
        return $this; // method chaining
    }
}
?>
