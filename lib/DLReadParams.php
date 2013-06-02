<?php

class DLReadParams {

    public $columns;
    public $filter;
    public $limit;
    public $skip;
    public $sort;
    public $total;

    public function __construct(
        $columns = NULL,
        $filter = NULL,
        $limit = NULL,
        $skip = NULL,
        $sort = NULL,
        $total = NULL
    ) {
        $this->columns = $columns;
        $this->filter = $filter;
        $this->limit = $limit;
        $this->skip = $skip;
        $this->sort = $sort;
        $this->total = $total;
    }

    public function sortAsc($column) {
        if ($this->sort == NULL) {
            $this->sort = array();
        }
        if (gettype($this->sort) !== 'array') {
            throw new Exception('DLReadParams.sort must be an array, but it is not');
        }
        array_push($this->sort, $column . ':$asc');
        return $this; // method chaining
    }

    public function sortDesc($column) {
        if ($this->sort == NULL) {
            $this->sort = array();
        }
        if (gettype($this->sort) !== 'array') {
            throw new Exception('DLReadParams.sort must be an array, but it is not');
        }
        array_push($this->sort, $column . ':$desc');
        return $this; // method chaining
    }
}
?>
