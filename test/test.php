<?php

require_once(dirname(__FILE__) . '/../Datalanche.php');

function startsWith($haystack, $needle) {
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function convertSimpleFilter($filter) {

    if (gettype($filter) !== 'array') {
        return $filter;
    }

    $hasNot = false;

    $keys = array_keys($filter);
    $field = $keys[0];
    $opExpr = $filter[$field];

    $keys = array_keys($opExpr);
    $operator = $keys[0];
    $value = $opExpr[$operator];

    if ($operator === '$not') {
        $hasNot = true;
        if (gettype($value) === 'array') {
            $keys = array_keys($value);
            $operator = $keys[0];
            $value = $value[$operator];
        }
    }

    $newFilter = new DLFilter();

    if ($operator === '$ends') {
        if ($hasNot === false) {
            $newFilter->field($field)->endsWith($value);
        } else {
            $newFilter->field($field)->notEndsWith($value);
        }
    } else if ($operator === '$contains') {
        if ($hasNot === false) {
            $newFilter->field($field)->contains($value);
        } else {
            $newFilter->field($field)->notContains($value);
        }
    } else if ($operator === '$eq') {
        if ($hasNot === false) {
            $newFilter->field($field)->equals($value);
        } else {
            $newFilter->field($field)->notEquals($value);
        }
    } else if ($operator === '$gt') {
        if ($hasNot === false) {
            $newFilter->field($field)->greaterThan($value);
        } else {
            $newFilter->field($field)->lessThanEqual($value);
        }
    } else if ($operator === '$gte') {
        if ($hasNot === false) {
            $newFilter->field($field)->greaterThanEqual($value);
        } else {
            $newFilter->field($field)->lessThan($value);
        }
    } else if ($operator === '$in') {
        if ($hasNot === false) {
            $newFilter->field($field)->anyIn($value);
        } else {
            $newFilter->field($field)->notAnyIn($value);
        }
    } else if ($operator === '$lt') {
        if ($hasNot === false) {
            $newFilter->field($field)->lessThan($value);
        } else {
            $newFilter->field($field)->greaterThanEqual($value);
        }
    } else if ($operator === '$lte') {
        if ($hasNot === false) {
            $newFilter->field($field)->lessThanEqual($value);
        } else {
            $newFilter->field($field)->greaterThan($value);
        }
    } else if ($operator === '$starts') {
        if ($hasNot === false) {
            $newFilter->field($field)->startsWith($value);
        } else {
            $newFilter->field($field)->notStartsWith($value);
        }
    }

    return $newFilter;
}

function convertFilter($filter) {

    if (gettype($filter) !== 'array') {
        return $filter;
    }

    //print_r($filter);

    $keys = array_keys($filter);
    $operator = $filter[$keys[0]];

    if ($operator === '$and' || $operator === '$or') {
        $filterList = $filter[$operator];

        $newFilterList = array();
        for ($i = 0; i < count($filterList); $i++) {
            push_array($newFilterList, convertFilter($filterList[$i]));
        }

        $newFilter = new DLFilter();

        if ($operator === '$and') {
            $newFilter->boolAnd($newFilterList);
        } else {
            $newFilter->boolOr($newFilterList);
        }

        return $newFilter;
    } else {
        return convertSimpleFilter($filter);
    }
}

function convertSort($value) {
    $newstr = '';

    if (gettype($value) === 'boolean') {
        if ($value === true) {
            $newstr = 'true';
        } else {
            $newstr = 'false';
        }
    } else if (gettype($value) === 'array') {
        for ($i = 0; $i < count($value); $i++) {
            if (gettype($value[$i]) === 'boolean') {
                if ($value[$i] === true) {
                    $newstr = $newstr . 'true';
                } else {
                    $newstr = $newstr . 'false';
                }
            } else {
                $newstr = $newstr . $value[$i];
            }

            if ($i < count($value) - 1) {
                $newstr = $newstr . ',';
            }
        }
    } else {
        $newstr = (string)$value;
    }
    
    return $newstr;
}

function handleTest($data, $test) {
    $result = 'FAIL';

    if ($data === $test['expected']['data']) {
        $result = 'PASS';
    }

    $output = array();
    $output['name'] = $test['name'];
    $output['expected'] = $test['expected'];
    $output['actual'] = array();
    $output['actual']['statusCode'] = 200;
    $output['actual']['exception'] = '';
    $output['actual']['data'] = $data;
    $output['result'] = $result;

    echo json_encode($output) . "\n";

    if ($result === 'PASS') {
        return true;
    }
    return false;
}

function handleException($e, $test) {
    $result = 'FAIL';

    $exception = json_decode($e->getMessage(), true);

    if ($e->getCode() === $test['expected']['statusCode']
        and $exception['message'] === $test['expected']['data']
        and $exception['code'] === $test['expected']['exception']) {
        $result = 'PASS';
    }

    $output = array();
    $output['name'] = $test['name'];
    $output['expected'] = $test['expected'];
    $output['actual'] = array();
    $output['actual']['statusCode'] = $e->getCode();
    $output['actual']['exception'] = $exception['code'];
    $output['actual']['data'] = $exception['message'];
    $output['result'] = $result;

    echo json_encode($output) . "\n";

    if ($result === 'PASS') {
        return true;
    }
    return false;
}

function getList($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $data = $client->getList();
        $success = handleTest($data, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function getSchema($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $data = $client->getSchema($test['parameters']['dataset']);
        $success = handleTest($data, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function read($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);

        $params = new DLReadParams();
        if (array_key_exists('dataset', $test['parameters']) === true) {
            $params->dataset = $test['parameters']['dataset'];
        }
        if (array_key_exists('fields', $test['parameters']) === true) {
            $params->fields = $test['parameters']['fields'];
        }
        if (array_key_exists('filter', $test['parameters']) === true) {
            $params->filter = convertFilter($test['parameters']['filter']);
        }
        if (array_key_exists('limit', $test['parameters']) === true) {
            $params->limit = $test['parameters']['limit'];
        }
        if (array_key_exists('skip', $test['parameters']) === true) {
            $params->skip = $test['parameters']['skip'];
        }
        if (array_key_exists('sort', $test['parameters']) === true) {
            $params->sort = convertSort($test['parameters']['sort']);
        }
        if (array_key_exists('total', $test['parameters']) === true) {
            $params->total = $test['parameters']['total'];
        }

        $data = $client->read($params);
        $success = handleTest($data, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

if (count($argv) < 3 || count($argv) > 6) {
    echo "ERROR: invalid format: php test.php <apikey> <testdir> [ <host> <port> <verifyssl> ]\n";
    exit(1);
}

$numPassed = 0;
$totalTests = 0;
$validKey = $argv[1];
$dirname = $argv[2];

$host = NULL;
if (count($argv) >= 4) {
    $host = $argv[3];
}

$port = NULL;
if (count($argv) >= 5) {
    $port = $argv[4];
}

$verifySsl = true;
if (count($argv) >= 6) {
    $verifySsl = strtolower($argv[5]);
    if ($verifySsl === '0' || $verifySsl === 'false') {
        $verifySsl = false;
    } else {
        $verifySsl = true;
    }
}

$client = new DLClient('', '', $host, $port, $verifySsl);

$files = new DirectoryIterator($dirname);
while ($files->valid()) {
    $filename = $files->current()->getFilename();

    if (endsWith($filename, '.json') === true) {

        echo $filename . "\n";

        $jsondata = json_decode(file_get_contents($dirname . '/' . $filename), true);
        $numTests = count($jsondata['tests']);
        $totalTests = $totalTests + $numTests;

        for ($i = 0; $i < $numTests; $i++) {

            $test = $jsondata['tests'][$i];

            // skip test if php listed
            if (array_key_exists('skip_languages', $test) === true && in_array('php', $test['skip_languages']) === true) {
                $totalTests = $totalTests - 1;
                continue;
            }

            if ($test['parameters']['key'] === 'valid_key') {
                $test['parameters']['key'] = $validKey;
            }

            $success = false;

            if ($test['method'] === 'list') {
                $success = getList($client, $test);
            } else if ($test['method'] === 'schema') {
                $success = getSchema($client, $test);
            } else if ($test['method'] === 'read') {
                $success = read($client, $test);
            } else {
                echo 'ERROR: ' . $test['method'] . " method not found\n";
            }

            if ($success === true) {
                $numPassed = $numPassed + 1;
            }
        }
    }

    $files->next();
}

echo "-------------------------------\n";
echo 'passed: ' . $numPassed . "\n";
echo 'failed: ' . ($totalTests - $numPassed) . "\n";
echo 'total:  ' . $totalTests . "\n";
