<?php

include '../lib/client.php';

class datalancheTestSequence
{
    public $deployed_client_parameters;
    public $runtime_messages;

    public function __construct($_secret, $_key, $_host, $_port, $_ssl, $_test_dataset_path, $_test_suite)
    {
        $this->deployed_client_parameters = array(
            'secret' => null,
            'key' => null,
            'port' => null,
            'test_dataset_path' => null,
            'ssl' => true,
            'test_suite' => null,
            'host' => 'api.datalanche.com'
            );
        $this->runtime_messages = array();

        if($_secret !== null)
        {
            $this->deployed_client_parameters['secret'] = $_secret;
        }
        if($_key !== null)
        {
            $this->deployed_client_parameters['key'] = $_key;
        }
        if($_host !== null)
        {
            $this->deployed_client_parameters['host'] = $_host;
        }
        if($_port !== null)
        {
            $this->deployed_client_parameters['port'] = $_port;
        }
        if($_ssl !== null)
        {
            $this->deployed_client_parameters['ssl'] = $_ssl;
        }
        if($_test_suite !== null)
        {
            $this->deployed_client_parameters['test_suite'] = $_test_suite;
        }elseif($_test_suite === null){
            $this->deployed_client_parameters['test_suite'] = 'all';
        }
        if($_test_dataset_path !== null)
        {
            $this->deployed_client_parameters['test_dataset_path'] = $_test_dataset_path;
        }elseif($_test_dataset_path === null){
            throw new Exception("THE PATH FOR DATASET TEST CONTENT WAS NULL\n");
            exit();
        }

        return($this);
    }
    
    public function startsWith ($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    public function endsWtih ($haystack, $needle)
    {
        $length = stlen($needle);
        if ($length == 0)
        {
            return true;
        }

        return (substr($haystack, -$length) === $needle);   
    }

    public function scrobbleTestAuth($json, $relevant_parameters)
    {

        for($i = 0; $i < count($json); $i++)
        {
            foreach($json[$i]['tests'] as $key => $test)
            {

                if($test['parameters']['key'] === 'valid_key')
                {
                    $json[$i]['tests'][$key]['parameters']['key'] = $relevant_parameters['key'];
                }
                if($test['parameters']['secret'] === 'valid_secret')
                {
                    $json[$i]['tests'][$key]['parameters']['secret'] = $relevant_parameters['secret'];
                }
            }
        }

        return($json);
    }

    public function addTests($tests, $json)
    {
        //$json = scrobbleTestAuth($json, $this->deployed_client_parameters);

        foreach($json['tests'] as $_test)
        {
            array_push($tests, $_test);
        }

        return($tests);
    }

    public function getRecordsFromFile($filename) 
    {
        $records = array();
        $i = 0;
        $fp = fopen($filename, 'r') or die("can't open file");

        while ($row = fgetcsv($fp, 10000)) 
        {
            if ($i !== 0) 
            {
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

    public function handleTestResult($test, $datalanche_error, $data)
    {
        $_result = 'FAIL';
        $_message = null;
        $_acutal_results = array (
            'status_code' => null,
            'exception' => null,
            'data' => null
            );

        if($datalanche_error != null)
        {
            //$message = $datalanche_error['body']['message'];
            /*
            if($this->startsWith($message, 'error:') === true)
            {
                $message = str_replace('error:', 'Error:', $message);
            }
            */
            //$_acutal_results['status_code'] = $datalanche_error['statusCode'];
            //$_acutal_results['exception'] = $datalanche_error['body']['code'];
           // $_acutal_results['data'] = $datalanche_error['message'];
        }else {
            /*
            $_acutal_results['status_code'] = 200;
            $_acutal_results['data'] = $data;
            */
        }
       // echo "----------------------------------------\n";
        //echo "EXPECTED: ".$test['expected']['statusCode']."\n";
        //echo "ACTUAL: ".$datalanche_error['statusCode']."\n";
        //echo "TEST INFO:\n";
        //var_dump($test['expected']);

        if($test['expected']['statusCode'] === $datalanche_error['statusCode'])
        {
            echo "----------------------------------------\n";
            $_result = 'PASS';
            echo $_result.":".$test['name']."\n";
            echo "EXPECTED: ".$test['expected']['statusCode']." RECEIVED: ".$datalanche_error['statusCode']."\n";
            var_dump($test['parameters']);
            echo "----------------------------------------\n";
            return(true);
        }else
        {
            echo "----------------------------------------\n";
            echo $_result.":".$test['name']."\n";
            echo "EXPECTED: ".$test['expected']['statusCode']." RECEIVED: ".$datalanche_error['statusCode']."\n";
            var_dump($test['parameters']);
            var_dump(debug_backtrace());
            echo "----------------------------------------\n";
            exit();
            return(false);
        }
        //debug_print_backtrace();
         //echo "///////////////////////////////\n";
    }

    //ask what is up with this func
    public function useRawQuery($keys, $params)
    {
        $use_raw = false;
        foreach($keys as $key)
        {
            if(array_key_exists($key, $params) === false)
            {
                echo "using raw!\n";
                $use_raw = true;
                break;
            }
        }
        return($use_raw);
    }

    //  WHAT IS $URL SUPPOSED TO BE? FULL URL?

    public function queryRaw($client, $test, $type, $url, $body)
    {
        $_results = null;
        $_pass = false;
        $curl_request = null;
        $http_auth_string = $test['parameters']['key'].":".$test['parameters']['secret'];
        $curl_request = null;
        echo "QUERY RAW INITIALIZED\n";


        if($type === 'del')
        {
            $query_raw_query = new stdClass();
            $query_raw_query->base_url = $url;
            $query_raw_query->parameters = array('name' => $test['parameters']['name']);
            $request_url = $client->getUrl($query_raw_query);
            $curl_request = $client->curlCreator();
            curl_setopt($curl_request, CURLOPT_URL, $request_url);
            curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($curl_request, CURLOPT_USERPWD, $http_auth_string);
            $_results = $client->handleResults($curl_request);
            $_results = $this->handleTestResult($test, $_results, null);
            if($_results){$_pass=true;}
            return($_pass);
        } elseif ($type === 'post')
        {
            $request_url = $client->url.$url;
            $curl_request = $client->curlCreator();
            curl_setopt($curl_request, CURLOPT_URL, $request_url);
            curl_setopt($curl_request, CURLOPT_POST, true);
            curl_setopt($curl_request, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            if(array_key_exists('where', $body))
            {
                echo "found where clause\n";
                $body['where'] = json_encode($body['where']);
            }
            curl_setopt($curl_request, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($curl_request, CURLOPT_USERPWD, $http_auth_string);
            $_results = $client->handleResults($curl_request);
            $_results = $this->handleTestResult($test, $_results, $body);
            if($_results){$_pass=true;}
            return($_pass);
        } elseif ($type === 'get')
        {
            $query_raw_query = new stdClass();
            $query_raw_query->base_url = $url;
            $query_raw_query->parameters = array('name' => $test['parameters']['name']);
            $request_url = $client->getUrl($query_raw_query);
            $curl_request = $client->curlCreator();
            curl_setopt($curl_request, CURLOPT_URL, $url);
            curl_setopt($curl_request, CURLOPT_USERPWD, $http_auth_string);
            $_results = $client->handleResults($curl_request);
            $_results = $this->handleTestResult($test, $_results, null);
            if($_results){$_pass=true;}
            return($_pass);
        }
    }

    public function alterTable($test, $client)
    {
        $_results = null;
        $params = $test['parameters'];
        $keys = array(
            'name',
            'rename',
            'description',
            'is_private',
            'license',
            'sources',
            'add_columns',
            'drop_columns',
            'alter_columns'
            );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params['key']); unset($params['secret']);
            $_results = $this->queryRaw($client, $test, 'post', '/alter_table', $params);
            $_results = $this->handleTestResult($test, $_results, $params);
            return($_results);
        }else 
        {
            $query = new Query($params['key'], $params['secret']);
            unset($params['key']); unset($params['secret']);
            $query->alterTable($params['name']);
            $query->rename($params['rename']);
            $query->description($params['description']);
            $query->isPrivate($params['is_private']);
            $query->license($params['license']);
            $query->sources($params['sources']);
            if(is_array($params['add_columns']) === true)
            {
                for($i = 0; $i < count($params['add_columns']); $i++)
                {
                    $query->addColumn($params['add_columns'][$i]);
                }
            } else {
                $query->parameters['add_columns'] = $params['add_columns'];
            }

            if(is_array($params['drop_columns']) === true)
            {
                for($i = 0; $i < count($params['drop_columns']); $i++)
                {
                    $query->addColumn($params['drop_columns'][$i]);
                }
            }else {
                $query->parameters['drop_columns'] = $params['drop_columns'];
            }

            if(is_object($params['alter_columns']) === true && count(get_object_vars($params)) > 0)
            {
                foreach($params['alter_columns'] as $key => $value)
                {
                    $query->alterColumn($key, $value);
                }
            } else {
                $query->parameters['alter_columns'] = $params['alter_columns'];
            }

            $_results = $client->query($query);
            $_results = handleTestResult($test, $_results, $params);

            return($_results);
        }

        //implement unset for params before passing
    }

    public function createTable($test, $client)
    {
        echo "we are in create_table\n";
        $params = $test['parameters'];
        $use_raw = null;
        $_results = null;
        $_query = null;
        $keys = array(
            'name',
            'description',
            'is_private',
            'license',
            'sources',
            'columns'
            );

        $use_raw = $this->useRawQuery($keys, $params);
        //echo $use_raw."\n";
        //exit();

        if($use_raw === true)
        {
            unset($params['key']); unset($params['secret']);
            $_results = $this->queryRaw($client, $test, 'post', '/create_table', $params);
            return($_results);
        } else
        {

            $_query = new Query($params['key'], $params['secret']);
            unset($params['key']); unset($params['secret']);
            $_query->createTable($params['name']);
            $_query->description($params['description']);
            $_query->isPrivate($params['is_private']);
            $_query->license($params['license']);
            $_query->sources($params['sources']);
            $_query->columns($params['columns']);

            $_results = $client->query($_query);
            $_results = $this->handleTestResult($test, $_results, $params);

            return($_results);
        }
    }

    public function dropTable($test, $client)
    {
        $use_raw = null;
        $_results = null;
        $_query = null;
        $params = $test['parameters'];
        $keys = array( 'name' );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params['key']); unset($params['secret']);
            $_results = $this->queryRaw($client, $test, 'del', '/drop_table', $params);
            return($_results);
        } else
        {
            $_query = new Query($params['key'], $params['secret']);
            unset($params['key']); unset($params['secret']);
            $_query->dropTable($params['name']);
            $_results = $client->query($_query);
            $_results = $this->handleTestResult($test, $_results, $params);
            return($_results);
        }
    }

    public function deleteFrom($test, $client)
    {
        $_results = null;
        $_query = null;
        $use_raw = null;
        $params = $test['parameters'];
        $keys = array( 'name', 'where' );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params['key']); unset($params['secret']);
            $_results = $this->queryRaw($client, $test, 'post', '/delete_from', $params);
            return($_results);
        } else
        {
            $_query = new Query($params['key'], $params['secret']);
            $_query->deleteFrom($params['name']);
            $_query->where(json_encode($params['where']));
            echo "this should be it\n";
            var_dump($params['where']);
            $_results = $client->query($_query);
            $_results = $this->handleTestResult($test, $_results, $params);
            return($_results);
        }
    }

    public function getTableList($test, $client)
    {
        $_results = null;
        $_query = null;
        $use_raw = null;
        $params = $test['parameters'];
        $keys = array();

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params['key']); unset($params['secret']);
            $_results = $this->queryRaw($client, $test, 'get', '/get_table_list', $params);
            return($_results);
        } else
        {
            if($test['expected']['statusCode'] == 200)
            {
                $_query = new Query($params['key'], $params['secret']);
                unset($params['key']); unset($params['secret']);
                $_query->getTableList();
                $_results = $client->query($_query);

            }
            
            
            $_results = $this->handleTestResult($test, $_results, $params);
            return($_results);
        }
    }

    public function getTableInfo($test, $client)
    {
        $_query = null;
        $_results = null;
        $use_raw = null;
        $params = $test['parameters'];
        $keys = array( 'name' );

        $use_raw = $this->useRawQuery($keys, $params);
        
        if($use_raw === true)
        {
            unset($params['key']); unset($params['secret']);
            $_results = $this->queryRaw($client, $test, 'get', '/get_table_info', $params);
            return($_results);
        } else
        {
            $_query = new Query($params['key'], $params['secret']);
            unset($params['key']); unset($params['secret']);
            $_query->getTableInfo($params['name']);
            $_results = $client->query($_query);
            $_results = $this->handleTestResult($test, $_results, $params);
            return($_results);
        }
    }

    public function insertInto($test, $client)
    {
        $_results = null;
        $_path = null;
        $_query = null;
        $_rows = null;
        $use_raw = null;
        $params = $test['parameters'];
        $keys = array( 'name', 'values' );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params['key']); unset($params['secret']);
            $_results = $this->queryRaw($client, $test, 'post', '/insert_into', $params);
            return($_results);
        }else
        {
            $_query = new Query($params['key'], $params['secret']);
            unset($params['key']); unset($params['secret']);
            $_query->insertInto($params['name']);

            if($params['values'] === 'dataset_file')
            {
                var_dump($test);
                $_path = realpath("../../api-server/tests/".$test['dataset_file']);
                $_rows = $this->getRecordsFromFile($_path);
                $_query->values($_rows);
                $_results = $client->query($_query);
                $_results = $this->handleTestResult($test, $_results, $params);
                return($_results);
            }else
            {
                $_query->values($params['values']);
                $_results = $client->query($_query);
                $_results = $this->handleTestResult($test, $_results, $params);
                return($_results);
            }
        }
    }

    public function selectFrom($test, $client)
    {
        $use_raw = null;
        $_results = null;
        $_query = null;
        $params = $test['parameters'];
        $keys = array(
            'select',
            'distinct',
            'from',
            'where',
            'group_by',
            'order_by',
            'offset',
            'limit',
            'total'
            );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params['key']); unset($params['secret']);
            $_results = $this->queryRaw($client, $test, 'post', '/select_from', $params);
            return($_results);
        }else
        {
            $_query = new Query($params['key'], $params['secret']);
            unset($params['key']); unset($params['secret']);
            $_query->select($params['select']);
            $_query->distinct($parmas['distinct']);
            $_query->from($params['from']);
            $_query->where(json_encode($params['where']));
            echo "this should be it\n";
            var_dump($params['where']);;
            $_query->groupBy($params['group_by']);
            $_query->orderBy($params['order_by']);
            $_query->offset($params['offset']);
            $_query->limit($params['limit']);
            $_query->total($params['total']);

            $_results = $client->query($_query);
            $_results = $this->handleTestResult($test, $_results, $params);
            return($_results);
        }
    }

    public function update($test, $client)
    {
        $use_raw = null;
        $_results = null;
        $_query = null;
        $params = $test['parameters'];
        $keys = array ('name', 'set', 'where');

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params['key']); unset($params['secret']);
            $_results = $this->queryRaw($client, $test, 'post', '/update', $params);
            return($_results);
        }else
        {
            $_query = new Query($params['key'], $params['secret']);
            $_query->update($params['name']);
            $_query->set($params['set']);
            $_query->where(json_encode($params['where']));
            echo "this should be it:\n";
            var_dump($params['where']);
            $_results = $client->query($_query);
            $_results = $this->handleTestResult($test, $_results, $params);
            return($_results);
        }
    }

    public function execute($test, $client)
    {
        $_results = null;
        $compare = (string) $test['method'];

        if($compare === 'alter_table')
        {
            $_results = $this->alterTable($test, $client);
        }
        if($compare === 'create_table')
        {
            $_results = $this->createTable($test, $client);
        }
        if($compare === 'delete_from')
        {
            $_results = $this->deleteFrom($test, $client);
        }
        if($compare === 'drop_table')
        {
            $_results = $this->dropTable($test, $client);
        }
        if($compare === 'get_table_info')
        {
            echo "in get info\n";
            $_results = $this->getTableInfo($test, $client);
        }
        if($compare === 'get_table_list')
        {
             echo "in get info\n";
            $_results = $this->getTableList($test, $client);
        }
        if($compare === 'insert_into')
        {
            $_results = $this->insertInto($test, $client);
        }
        if($compare === 'select_from')
        {
            $_results = $this->selectFrom($test, $client);
        }
        if($compare === 'update')
        {
            $_results = $this->update($test, $client);
        }

        if($_results === null)
        {
            var_dump($test);
            echo $compare."\n";
            throw new Exception("datalancheTestSequence->execute() has reported being out of focus. [FATAL]\n");
            exit();
        }

        return($_results);
    }

    public function cleanSets($client)
    {
        $_query = new Query(null, null);
        try
        {
            $client->query($_query->dropTable('test_dataset'));
            $client->query($_query->dropTable('new_test_dataset'));
        }catch(Exception $e){
            echo $e."\n";
        }
    }

    public function adventSequence()
    {
        $tests = array();
        $_file = null;
        $ex_param = array(
            'key' => $this->deployed_client_parameters['key'],
            'secret' => $this->deployed_client_parameters['secret']
            );
        $_json = null;
        $_results = null;
        $number_tests_passed = null;
        $_dataset_path = $this->deployed_client_parameters['test_dataset_path'];
        $contents = json_decode(file_get_contents($_dataset_path), true);
        $root_dir = realpath(dirname($_dataset_path));
        $sub_test_path = $root_dir.'/'.$contents['dataset_file'];

        for($i = 0; $i < count($contents['suites'][$this->deployed_client_parameters['test_suite']]); $i++)
        {
            $_file = $root_dir.'/'.$contents['suites']['all'][$i];
            //echo $_file."\n";
            $_json = json_decode(file_get_contents($_file), true);
            array_push($tests, $_json);
        }

        $client = new Client($this->deployed_client_parameters['secret'],
                             $this->deployed_client_parameters['key'],
                             $this->deployed_client_parameters['host'],
                             $this->deployed_client_parameters['port'],
                             $this->deployed_client_parameters['ssl']);

        $this->cleanSets($client);
        $tests = $this->scrobbleTestAuth($tests, $ex_param);

        for($i = 0; $i < count($tests); $i++)
        {
            foreach($tests[$i]['tests'] as $key => $test)
            {
                $_results = $this->execute($test, $client);
                if($_results)
                {
                    $number_tests_passed++;
                }
            }
        }
        echo "\n------\n".$number_tests_passed."\n------\n";
        exit();

    }
}


    $test_file_path = $argv[2];
    $key = $argv[4];
    $secret = $argv[6];
    $host = $argv[8];
    $port = $argv[10];
    $ssl = $argv[12];
    $test_sequence = new datalancheTestSequence($secret, $key, $host, $port, $ssl, $test_file_path, null);
    $test_sequence->adventSequence();
?>