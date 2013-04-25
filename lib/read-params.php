<?php

class DLReadParams {

    public $dataset;
    public $fields;
    public $filter;
    public $limit;
    public $skip;
    public $sort;
    public $total;

    public function __construct(
        $dataset = NULL,
        $fields = NULL,
        $filter = NULL,
        $limit = NULL,
        $skip = NULL,
        $sort = NULL,
        $total = NULL
    ) {
        $this->dataset = $dataset;
        $this->fields = $fields;
        $this->filter = $filter;
        $this->limit = $limit;
        $this->skip = $skip;
        $this->sort = $sort;
        $this->total = $total;
    }

    public function sortAsc($field) {
        if ($this->sort == NULL) {
            $this->sort = array();
        }
        if (gettype($this->sort) !== 'array') {
            throw new Exception('DLReadParams.sort must be an array, but it is not');
        }
        array_push($this->sort, $field . ':$asc');
        return $this; // method chaining
    }

    public function sortDesc($field) {
        if ($this->sort == NULL) {
            $this->sort = array();
        }
        if (gettype($this->sort) !== 'array') {
            throw new Exception('DLReadParams.sort must be an array, but it is not');
        }
        array_push($this->sort, $field . ':$desc');
        return $this; // method chaining
    }
}
?>
