<?php

class DLFilter {

    private $hasNot;
    private $field;
    private $filters;
    private $operator;
    private $value;

    public function __construct() {
        $this->hasNot = false;
        $this->field = NULL;
        $this->filters = NULL;
        $this->operator = NULL;
        $this->value = NULL;
    }

    public function __toString() {
        return json_encode($this->json());
    }

    public function json() {

        if ($this->operator === '$and' || $this->operator === '$or') {

            if ($this->filters == NULL) {
                throw new Exception('filter array = NULL in DLFilter');
            }
            if ($this->field !== NULL) {
                throw new Exception('field cannot be set when $and, $or used in DLFilter');
            }

            $jsonList = array();
            for ($i = 0; $i < count($this->filters); $i++) {
                $item = NULL;
                if ($this->filters[i] instanceof DLFilter) {
                    $item = $this->filters[i]->json();
                } else {
                    $item = $this->filters[i];
                }

                array_push($jsonList, $item);
            }

            $json = array();
            $json[(string)$this->operator] = $jsonList;

            return $json;

        } else {

            if ($this->field === NULL) {
                throw new Exception('field = NULL in DLFilter');
            }
            if ($this->operator === NULL) {
                throw new Exception('operator = NULL in DLFilter');
            }
            if ($this->value === NULL) {
                throw new Exception('value = NULL in DLFilter');
            }

            $opExpr = array();
            $opExpr[(string)$this->operator] = $this->value;

            if ($this->hasNot === true) {
                $tempExpr = array();
                $tempExpr['$not'] = $opExpr;
                $opExpr = $tempExpr;
            }

            $json = array();
            $json[(string)$this->field] = $opExpr;

            return $json;
        }
    }

    public function field($string) {
        $this->field = $string;
        return $this; // method chaining
    }

    public function boolAnd($filterArray) {
        $this->filters = $filterArray;
        $this->operator = '$and';
        return $this; // method chaining
    }

    public function boolOr($filterArray) {
        $this->filters = $filterArray;
        $this->operator = '$or';
        return $this; // method chaining
    }

    public function contains($value) {
        $this->hasNot = false;
        $this->operator = '$contains';
        $this->value = $value;
        return $this; // method chaining
    }

    public function endsWith($value) {
        $this->hasNot = false;
        $this->operator = '$ends';
        $this->value = $value;
        return $this; // method chaining
    }

    public function equals($value) {
        $this->hasNot = false;
        $this->operator = '$eq';
        $this->value = $value;
        return $this; // method chaining
    }

    public function greaterThan($value) {
        $this->hasNot = false;
        $this->operator = '$gt';
        $this->value = $value;
        return $this; // method chaining
    }

    public function greaterThanEqual($value) {
        $this->hasNot = false;
        $this->operator = '$gte';
        $this->value = $value;
        return $this; // method chaining
    }

    public function anyIn($value) {
        $this->hasNot = false;
        $this->operator = '$in';
        $this->value = $value;
        return $this; // method chaining
    }

    public function lessThan($value) {
        $this->hasNot = false;
        $this->operator = '$lt';
        $this->value = $value;
        return $this; // method chaining
    }

    public function lessThanEqual($value) {
        $this->hasNot = false;
        $this->operator = '$lte';
        $this->value = $value;
        return $this; // method chaining
    }

    public function notContains($value) {
        $this->hasNot = true;
        $this->operator = '$contains';
        $this->value = $value;
        return $this; // method chaining
    }

    public function notEndsWith($value) {
        $this->hasNot = true;
        $this->operator = '$ends';
        $this->value = $value;
        return $this; // method chaining
    }

    public function notEquals($value) {
        $this->hasNot = true;
        $this->operator = '$eq';
        $this->value = $value;
        return $this; // method chaining
    }

    public function notAnyIn($value) {
        $this->hasNot = true;
        $this->operator = '$in';
        $this->value = $value;
        return $this; // method chaining
    }

    public function notStartsWith($value) {
        $this->hasNot = true;
        $this->operator = '$starts';
        $this->value = $value;
        return $this; // method chaining
    }

    public function startsWith($value) {
        $this->hasNot = false;
        $this->operator = '$starts';
        $this->value = $value;
        return $this; // method chaining
    }
}
?>
