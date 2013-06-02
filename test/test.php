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
    $column = $keys[0];
    $opExpr = $filter[$column];

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
            $newFilter->column($column)->endsWith($value);
        } else {
            $newFilter->column($column)->notEndsWith($value);
        }
    } else if ($operator === '$contains') {
        if ($hasNot === false) {
            $newFilter->column($column)->contains($value);
        } else {
            $newFilter->column($column)->notContains($value);
        }
    } else if ($operator === '$eq') {
        if ($hasNot === false) {
            $newFilter->column($column)->equals($value);
        } else {
            $newFilter->column($column)->notEquals($value);
        }
    } else if ($operator === '$gt') {
        if ($hasNot === false) {
            $newFilter->column($column)->greaterThan($value);
        } else {
            $newFilter->column($column)->lessThanEqual($value);
        }
    } else if ($operator === '$gte') {
        if ($hasNot === false) {
            $newFilter->column($column)->greaterThanEqual($value);
        } else {
            $newFilter->column($column)->lessThan($value);
        }
    } else if ($operator === '$in') {
        if ($hasNot === false) {
            $newFilter->column($column)->anyIn($value);
        } else {
            $newFilter->column($column)->notAnyIn($value);
        }
    } else if ($operator === '$lt') {
        if ($hasNot === false) {
            $newFilter->column($column)->lessThan($value);
        } else {
            $newFilter->column($column)->greaterThanEqual($value);
        }
    } else if ($operator === '$lte') {
        if ($hasNot === false) {
            $newFilter->column($column)->lessThanEqual($value);
        } else {
            $newFilter->column($column)->greaterThan($value);
        }
    } else if ($operator === '$starts') {
        if ($hasNot === false) {
            $newFilter->column($column)->startsWith($value);
        } else {
            $newFilter->column($column)->notStartsWith($value);
        }
    }

    return $newFilter;
}

function convertFilter($filter) {

    if (gettype($filter) !== 'array') {
        return $filter;
    }

    $keys = array_keys($filter);
    $operator = $keys[0];

    if ($operator === '$and' || $operator === '$or') {
        $filterList = $filter[$operator];

        $newFilterList = array();
        for ($i = 0; $i < count($filterList); $i++) {
            array_push($newFilterList, convertFilter($filterList[$i]));
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

function getRecordsFromFile($filename) {
    $records = array();
    $i = 0;
    $fp = fopen($filename, 'r') or die("can't open file");

    while ($row = fgetcsv($fp, 10000)) {
        if ($i !== 0) {
            array_push($records, array(
                'record_id' => $row[0],
                'name' => $row[1],
                'email' => $row[2],
                'address' => $row[3],
                'city' => $row[4],
                'state' => $row[5],
                'zip_code' => $row[6],
                'phone_number' => $row[7],
                'date_field' => $row[8],
                'time_field' => $row[9],
                'timestamp_field' => $row[10],
                'boolean_field' => $row[11],
                'int16_field' => $row[12],
                'int32_field' => $row[13],
                'int64_field' => $row[14],
                'float_field' => $row[15],
                'double_field' => $row[16],
                'decimal_field' => $row[17]
            ));
        }
        $i++;
    }

    fclose($fp) or die("can't close file");

    return $records;
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

function addColumns($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $client->addColumns($test['parameters']['dataset'], $test['body']['columns']);
        $success = handleTest(NULL, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function createDataset($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $client->createDataset($test['body']);
        $success = handleTest(NULL, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function deleteDataset($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $client->deleteDataset($test['parameters']['dataset']);
        $success = handleTest(NULL, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function deleteRecords($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $client->deleteRecords($test['parameters']['dataset'], convertFilter($test['parameters']['filter']));
        $success = handleTest(NULL, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function getDatasetList($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $data = $client->getDatasetList();

        // getDatasetList() test is a bit different than the rest
        // because a server can have any number of datasets. We test
        // that the expected dataset(s) is listed rather than
        // checking the entire result is valid, but only if a valid
        // response is expected.

        $datasets = array();
        
        for ($i = 0; $i < $data['num_datasets']; $i++) {
            $dataset = $data['datasets'][$i];

            // too variable to test
            try {
                unset($dataset['when_created']);
            } catch (Exception $e) {
                // ignore error
            }
            try {
                unset($dataset['last_updated']);
            } catch (Exception $e) {
                // ignore error
            }

            for ($j = 0; $j < $test['expected']['data']['num_datasets']; $j++) {
                if ($dataset === $test['expected']['data']['datasets'][$j]) {
                    array_push($datasets, $dataset);
                    break;
                }
            }
        }

        $data = array();
        $data['num_datasets'] = count($datasets);
        $data['datasets'] = $datasets;

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

        // Delete date/time properties since they are probably
        // different than the test data. This is okay because
        // the server sets these values on write operations.
        unset($data['when_created']);
        unset($data['last_updated']);

        $success = handleTest($data, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function insertRecords($client, $test, $filename) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        if ($test['body'] === 'dataset_file') {
            $records = getRecordsFromFile($filename);
            $client->insertRecords($test['parameters']['dataset'], $records);
        } else {
            $client->insertRecords($test['parameters']['dataset'], $test['body']['records']);
        }
        $success = handleTest(NULL, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function readRecords($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);

        $params = new DLReadParams();
        if (array_key_exists('columns', $test['parameters']) === true) {
            $params->columns = $test['parameters']['columns'];
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

        $data = $client->readRecords($test['parameters']['dataset'], $params);
        $success = handleTest($data, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function removeColumns($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $client->removeColumns($test['parameters']['dataset'], $test['parameters']['columns']);
        $success = handleTest(NULL, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function setDetails($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $client->setDetails($test['parameters']['dataset'], $test['body']);
        $success = handleTest(NULL, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function updateColumns($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $client->updateColumns($test['parameters']['dataset'], $test['body']);
        $success = handleTest(NULL, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

function updateRecords($client, $test) {
    $success = false;
    try {
        $client->setApiKey($test['parameters']['key']);
        $client->setApiSecret($test['parameters']['secret']);
        $client->updateRecords($test['parameters']['dataset'], $test['body'], convertFilter($test['parameters']['filter']));
        $success = handleTest(NULL, $test);
    } catch (DLException $e) {
        $success = handleException($e, $test);
    } catch (Exception $e) {
        echo $e . "\n";
    }
    return $success;
}

if (count($argv) < 4 || count($argv) > 7) {
    echo "ERROR: invalid format: php test.php <apikey> <apisecret> <testfile> [ <host> <port> <verifyssl> ]\n";
    exit(1);
}

$numPassed = 0;
$totalTests = 0;
$validKey = $argv[1];
$validSecret = $argv[2];
$testFile = $argv[3];

$host = NULL;
if (count($argv) >= 5) {
    $host = $argv[4];
}

$port = NULL;
if (count($argv) >= 6) {
    $port = $argv[5];
}

$verifySsl = true;
if (count($argv) >= 7) {
    $verifySsl = strtolower($argv[6]);
    if ($verifySsl === '0' || $verifySsl === 'false') {
        $verifySsl = false;
    } else {
        $verifySsl = true;
    }
}

$client = new DLClient($validKey, $validSecret, $host, $port, $verifySsl);

try {
    $client->deleteDataset('test_dataset');
} catch (Exception $e) {
    echo $e . "\n";
    // ignore error
}

try {
    $client->deleteDataset('new_test_dataset');
} catch (Exception $e) {
    echo $e . "\n";
    // ignore error
}

$testSuites = json_decode(file_get_contents($testFile), true);
$rootDir = dirname($testFile);
$datasetFile = $rootDir . '/' . $testSuites['dataset_file'];
$files = $testSuites['suites']['all'];

for ($j = 0; $j < count($files); $j++) {
    $filename = $files[$j];

    if (endsWith($filename, '.json') === true) {

        //echo $filename . "\n";

        $jsondata = json_decode(file_get_contents($rootDir . '/' . $filename), true);
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
            if ($test['parameters']['secret'] === 'valid_secret') {
                $test['parameters']['secret'] = $validSecret;
            }

            $success = false;

            if ($test['method'] === 'add_columns') {
                $success = addColumns($client, $test);
            } else if ($test['method'] === 'create_dataset') {
                $success = createDataset($client, $test);
            } else if ($test['method'] === 'delete_dataset') {
                $success = deleteDataset($client, $test);
            } else if ($test['method'] === 'delete_records') {
                $success = deleteRecords($client, $test);
            } else if ($test['method'] === 'insert_records') {
                $success = insertRecords($client, $test, $datasetFile);
            } else if ($test['method'] === 'get_dataset_list') {
                $success = getDatasetList($client, $test);
            } else if ($test['method'] === 'get_schema') {
                $success = getSchema($client, $test);
            } else if ($test['method'] === 'read_records') {
                $success = readRecords($client, $test);
            } else if ($test['method'] === 'remove_columns') {
                $success = removeColumns($client, $test);
            } else if ($test['method'] === 'set_details') {
                $success = setDetails($client, $test);
            } else if ($test['method'] === 'update_columns') {
                $success = updateColumns($client, $test);
            } else if ($test['method'] === 'update_records') {
                $success = updateRecords($client, $test);
            } else {
                echo 'ERROR: ' . $test['method'] . " method not found\n";
            }

            if ($success === true) {
                $numPassed = $numPassed + 1;
            }
        }
    }
}

echo "-------------------------------\n";
echo 'passed: ' . $numPassed . "\n";
echo 'failed: ' . ($totalTests - $numPassed) . "\n";
echo 'total:  ' . $totalTests . "\n";
