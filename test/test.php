<?php

include '../lib/client.php';
include 'raw_query.php';

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
    
    public function httpAssocEncode($array)
    {
        $requestOption = '?';
        $end = end($array);
        foreach($array as $key => $entry)
        {
            if($entry === null)
            {
                $entry = '';
            }
            if($entry === $end)
            {
                $requestOption = (string) $requestOption.urlencode($key).'='.urlencode($entry);
            } else {
                $requestOption = (string) $requestOption.urlencode($key).'='.urlencode($entry).'&';
            }
        }
        return($requestOption);
    }

     public function rawCurlCreator()
    {
        //same as $client->curlCreator()
        $options = array (
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_SSLVERSION => 3,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_USERAGENT => 'Datalanche PHP Client',
            CURLOPT_VERBOSE => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            );
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $options);
        return($curlHandle);
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

    public function isNotNull($r)
    {
        return !is_null($r);
    }

    public function scrobbleTestAuth($json, $relevant_parameters)
    {
        //var_dump($json);

        for($i = 0; $i < count($json); $i++)
        {
            foreach($json[$i]->tests as $key => $test)
            {
                //var_dump($test);
                
                if($test->parameters->secret === 'valid_secret')
                {
                    //echo "\nfound some valid\n";
                    $test->parameters->secret = $relevant_parameters['secret'];
                }
                if($test->parameters->key === 'valid_key')
                {
                    //echo "\nfound some key\n";
                    $test->parameters->key = $relevant_parameters['key'];
                }
                
            }  
        }
        //var_dump($json);
        //exit();
        return($json);
    }

    public function addTests($tests, $json)
    {
        //$json = scrobbleTestAuth($json, $this->deployed_client_parameters);

        foreach($json->tests as $_test)
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

        if($test->expected->statusCode === $datalanche_error['statusCode'])
        {
            //echo "----------------------------------------\n";
            $_result = 'PASS';
            echo $_result.":".$test->name."\n";
            echo "EXPECTED: ".$test->expected->statusCode." RECEIVED: ".$datalanche_error['statusCode']."\n";

            //var_dump($test['parameters']);
            echo "----------------------------------------\n";
            return(true);
        }else
        {
            //echo "----------------------------------------\n";
            echo $_result.":".$test->name."\n";
            echo "EXPECTED: ".$test->expected->statusCode." RECEIVED: ".$datalanche_error['statusCode']."\n";
            //echo $client->getClientSecret()."\n";
            //echo $client->getClientKey()."\n";
            var_dump($test);
            //var_dump(debug_backtrace());
            echo "----------------------------------------\n";
            if($test->name === "insert_into: values = one full row" || $test->name === "drop_table: table_name = array")
            {
                echo "We Found our test";
                $line = fgets(STDIN);
            }else {
                exit();
            }
            return(false);
        }
        //debug_print_backtrace();
         //echo "///////////////////////////////\n";
    }

    //ask what is up with this func
    public function useRawQuery($keys, $params)
    {
        //var_dump($keys);
        //var_dump($params);
        $use_raw = false;
        foreach($params as $key => $param)
        {
            if(in_array($key, $keys) === false)
            {
                echo "using raw!\n";
                echo "UNKNOWN VALUE: ";
                echo "[".$key."]\n";
                $use_raw = true;
                break;
            }
        }
        return($use_raw);
    }

    //  WHAT IS $URL SUPPOSED TO BE? FULL URL?

    public function queryRaw($client, $test, $type, $url, $body)
    {
        $results = null;
        $pass = false;
        $authString = $client->getClientKey().":".$client->getClientSecret();
        //echo "\n--------------------------------------------------\n";
        echo "QUERY RAW INITIALIZED\n";
        //echo "\n--------------------------------------------------\n";


        if($type === 'del')
        {
            //delete get request
            $requestUrl = $client->getClientUrl().$url.$this->httpAssocEncode($body);
            $curlHandle = $this->rawCurlCreator();
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($curlHandle, CURLOPT_USERPWD, $authString);
            curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);
            $results = $client->handleResults($curlHandle);
            $results = $this->handleTestResult($test, $results, $body);

            return($results); 

        } elseif ($type === 'get') {
            //get request
            $requestUrl = $client->getClientUrl().$url.$this->httpAssocEncode($body);
            $curlHandle = $this->rawCurlCreator();
            curl_setopt($curlHandle, CURLOPT_USERPWD, $authString);
            curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);
            $results = $client->handleResults($curlHandle);
            $results = $this->handleTestResult($test, $results, $body);

            return($results);

        } elseif ($type === 'post') {
            //echo "WE ARE IN POST!!\n";
            $requestUrl = $client->getClientUrl().$url;
            $curlHandle = $this->rawCurlCreator();

            echo "|BODY:____________________\n";
            //$body = json_encode($body);
            //$body = (string) json_encode($body);
            echo "\n";
            //echo gettype($body)."\n";
            //var_dump($body);
            echo json_encode($body)."\n";

            if(is_array($body) && count($body) === 0)
            {
                $body = null;
            }else{
            
                if($test->expected->statusCode === 200)
                {
                    $body = json_encode($body, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK);
                }else{
                    $body = json_encode($body, JSON_NUMERIC_CHECK);
                }
            }
            echo "|||||||||||||||||||||||||\n";
            
            curl_setopt($curlHandle, CURLOPT_USERPWD, $authString);
            //curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type application/json', 'Content-Length: '.strlen($body)));
            //curl_setopt($curlHandle, CURLOPT_POST, true);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $body);
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            
            



            curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);
            $results = $client->handleResults($curlHandle);
            $results = $this->handleTestResult($test, $results, $body);

            return($results);
        }
    }

    public function alterTable($test, $client)
    {
        $_results = null;
        $params = $test->parameters;
        $keys = array(
            'table_name',
            'rename',
            'description',
            'is_private',
            'license',
            'sources',
            'add_columns',
            'drop_columns',
            'alter_columns',
            'key',
            'secret'
            );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params->key); unset($params->secret);
            $_results = $this->queryRaw($client, $test, 'post', '/alter_table', $params);
            return($_results);
        }else 
        {
            $query = new Query();
            unset($params->key); unset($params->secret);
            if(property_exists($params, 'table_name') === true)
            {
                //echo "man\n";
                $query->alterTable($params->table_name);
            } else {
                //echo "fun\n";
                $query->setMethod('post');
                $query->setBaseUrl('/alter_table');
            }
            if(property_exists($params, 'rename'))
            {
                $query->rename($params->rename);
            }
            elseif(property_exists($params, 'description'))
            {
                $query->description($params->description);
            }
            elseif(property_exists($params, 'is_private'))
            {
                $query->isPrivate($params->is_private);
            }
            elseif(property_exists($params, 'license'))
            {
                $query->license($params->license);
            }
            elseif(property_exists($params, 'sources'))
            {
                $query->sources($params->sources);
            }
            if(property_exists($params, 'add_columns') && is_array($params->add_columns) === true)
            {
                for($i = 0; $i < count($params->add_columns); $i++)
                {
                    $query->addColumn($params->add_columns[$i]);
                }
            } elseif(property_exists($params, 'add_columns')) {
                $_results = $this->queryRaw($client, $test, 'post', '/alter_table', $params);
                return($_results);
            }
            if(property_exists($params, 'drop_columns') && is_array($params->drop_columns) === true)
            {
                for($i = 0; $i < count($params->drop_columns); $i++)
                {
                    $query->dropColumn($params->drop_columns[$i]);
                }
            }elseif(property_exists($params, 'drop_columns')) {
                $_results = $this->queryRaw($client, $test, 'post', '/alter_table', $params);
                return($_results);
            }

            if(property_exists($params, 'alter_columns') && is_object($params->alter_columns) === true && count(get_object_vars($params)) > 0)
            {
                foreach($params->alter_columns as $key => $value)
                {
                    $query->alterColumn($key, $value);
                }
            } elseif(property_exists($params, 'alter_columns')) {
                $_results = $this->queryRaw($client, $test, 'post', '/alter_table', $params);
                return($_results);
            }
            $_results = $client->query($query);
            $_results = $this->handleTestResult($test, $_results, $params);

            return($_results);
        }

        //implement unset for params before passing
    }

    public function createTable($test, $client)
    {
        echo "we are in create_table\n";
        $params = $test->parameters;
        $use_raw = null;
        $_results = null;
        $_query = null;
        $keys = array(
            'table_name',
            'description',
            'is_private',
            'license',
            'sources',
            'columns',
            'key',
            'secret'
            );

        $use_raw = $this->useRawQuery($keys, $params);
        //echo $use_raw."\n";
        //exit();

        if($use_raw === true)
        {
            echo "RAW CREATE\n";
            unset($params->key); unset($params->secret);
            $_results = $this->queryRaw($client, $test, 'post', '/create_table', $params);
            return($_results);
        } else
        {
            echo ("GOOD\n");

            $_query = new Query();
            unset($params->key); unset($params->secret);
            if(isset($params->table_name))
            {
                $_query->createTable($params->table_name);
            }else{
                if(property_exists($params, 'table_name'))
                {
                    echo "\nBALLLS!!\n";
                    $_query->createTable($params->table_name);
                } else {
                    $_query->setMethod('post');
                    $_query->setBaseUrl('/create_table');
                }
            }
            if(property_exists($params, 'description'))
            {
                $_query->description($params->description);
            }
            if(property_exists($params, 'is_private'))
            {  
                $_query->isPrivate($params->is_private);
            }
            if(property_exists($params, 'license'))
            {
                $_query->license($params->license);
            }
            if(property_exists($params, 'sources'))
            {
                $_query->sources($params->sources);
            }
            if(property_exists($params, 'columns'))
            {
                $_query->columns($params->columns);
            }
            //echo "create table\n";
            //var_dump($_query);
            echo "test\n";
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
        $params = $test->parameters;
        $keys = array( 'table_name', 'key', 'secret' );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params->key); unset($params->secret);
            $_results = $this->queryRaw($client, $test, 'del', '/drop_table', $params);
            return($_results);
        } else
        {
            $_query = new Query();
            unset($params->key); unset($params->secret);
            if(property_exists($params, 'table_name'))
            {
                if(is_bool($params->table_name))
                {
                    if($params->table_name === true)
                    {
                        $params->table_name = "true";
                    } elseif ($params->table_name === false) {
                        $params->table_name = "false";
                    }
                }
                $_query->dropTable($params->table_name);
            } else {
                $_query->setMethod('post');
                $_query->setBaseUrl('/drop_table');
                $_results = $this->queryRaw($client, $test, 'del', '/drop_table', $params);
                return($_results);
            }
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
        $params = $test->parameters;
        $keys = array( 'table_name', 'where', 'key', 'secret' );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params->key); unset($params->secret);
            $_results = $this->queryRaw($client, $test, 'post', '/delete_from', $params);
            return($_results);
        } else
        {
            $_query = new Query();
            if(property_exists($params, 'table_name'))
            {
                $_query->deleteFrom($params->table_name);
            }else {
                $_query->setMethod('post');
                $_query->setBaseUrl('/delete_from');
                $_results = $this->queryRaw($client, $test, 'post', '/delete_from', $params);
                return($_results);
            }
            if(property_exists($params, 'where'))
            {
                $_query->deleteFrom($params->table_name)->where($params->where);
            }
            echo "this should be it\n";
            //var_dump($params['where']);
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
        $params = $test->parameters;
        $keys = array('key', 'secret');

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params->key); unset($params->secret);
            echo "QUERY RAW QUERY RAW\n";
            $_results = $this->queryRaw($client, $test, 'get', '/get_table_list', $params);
            return($_results);
        } else
        {       if($test->expected === 200)
                {       
                    $_query = new Query();
                }else {
                    $_query= new Query();
                }
                unset($params->key); unset($params->secret);
                $_query->getTableList();
                $_results = $client->query($_query);
            
            
            $_results = $this->handleTestResult($test, $_results, $params);
            return($_results);
        }
    }

    public function getTableInfo($test, $client)
    {
        $_query = null;
        $_results = null;
        $use_raw = null;
        $params = $test->parameters;
        $keys = array( 'table_name', 'key', 'secret' );

        $use_raw = $this->useRawQuery($keys, $params);
        
        if($use_raw === true)
        {
            unset($params->key); unset($params->secret);
            $_results = $this->queryRaw($client, $test, 'get', '/get_table_info', $params);
            return($_results);
        } else
        {
            $_query = new Query();
            unset($params->key); unset($params->secret);
            if(property_exists($params, 'table_name') === true)
            {
                if($params->table_name === null)
                {
                    echo "SPECIAL FOUND\n";
                    $_results = $this->queryRaw($client, $test, 'get', '/get_table_info', $params);
                    return($_results);
                }else{
                    if(is_bool($params->table_name))
                    {
                        if($params->table_name === true)
                        {
                            $params->table_name = "true";
                        }elseif($params->table_name === false) {
                            $params->table_name = "false";
                        } 
                    }
                    $_query->getTableInfo($params->table_name);
                }
            } else {
                $_results = $this->queryRaw($client, $test, 'get', '/get_table_info', $params);
                return($_results);
            }
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
        $params = $test->parameters;
        $keys = array( 'table_name', 'values', 'key', 'secret' );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params->key); unset($params->secret);
            $_results = $this->queryRaw($client, $test, 'post', '/insert_into', $params);
            return($_results);
        }else
        {
            $_query = new Query();
            unset($params->key); unset($params->secret);
            if(property_exists($params, 'table_name'))
            {
                echo "found property\n";
                $_query->insertInto($params->table_name);
            }else {
                $_query->setMethod('post');
                $_query->setBaseUrl('/insert_into');
                $_results = $this->queryRaw($client, $test, 'post', '/insert_into', $params);
                return($_results);
            }

            if($params->values === 'dataset_file')
            {
                //var_dump($test);
                $_path = realpath("../../api-server/tests/".$test->dataset_file);
                $_rows = $this->getRecordsFromFile($_path);
                $_query->values($_rows);
                $_results = $client->query($_query);
                $_results = $this->handleTestResult($test, $_results, $params);
                return($_results);
            }else
            {
                if(property_exists($params, 'values'))
                {
                    $_query->values($params->values);
                }
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
        $params = $test->parameters;
        $keys = array(
            'select',
            'distinct',
            'from',
            'where',
            'group_by',
            'order_by',
            'offset',
            'limit',
            'total',
            'key',
            'secret',
            'table_name'
            );

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params->key); unset($params->secret);
            $_results = $this->queryRaw($client, $test, 'post', '/select_from', $params);
            return($_results);
        }else
        {
            $_query = new Query();
            unset($params->key); unset($params->secret);
            if(property_exists($params, 'select'))
            {
                $_query->select($params->select);
            } else {
                $_query->setMethod('post');
                $_query->setBaseUrl('/select_from');
                $_results = $this->queryRaw($client, $test, 'post', '/select_from', $params);
                return($_results);
            }
            if(property_exists($params, 'distinct'))
            {
                $_query->distinct($params->distinct);
            }
            if(property_exists($params, 'from'))
            {
                $_query->from($params->from);
            }
            if(property_exists($params, 'where'))
            {
                $_query->where($params->where);
            }
            //echo "this should be it\n";
            //var_dump($params['where']);;
            if(property_exists($params, 'group_by'))
            {
                $_query->groupBy($params->group_by);
            }
            if(property_exists($params, 'order_by'))
            {
                $_query->orderBy($params->order_by);
            }
            if(property_exists($params, 'offset'))
            {
                $_query->offset($params->offset);
            }
            if(property_exists($params, 'limit'))
            {
                $_query->limit($params->limit);
            }
            if(property_exists($params, 'total'))
            {
                $_query->total($params->total);
            }

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
        $params = $test->parameters;
        $keys = array ('table_name', 'set', 'where', 'key', 'secret');

        $use_raw = $this->useRawQuery($keys, $params);

        if($use_raw === true)
        {
            unset($params->key); unset($params->secret);
            $_results = $this->queryRaw($client, $test, 'post', '/update', $params);
            return($_results);
        }else
        {
            $_query = new Query();
            if(property_exists($params, 'table_name'))
            {
                $_query->update($params->table_name);
            } else {
                $_query->setMethod('post');
                $_query->setBaseUrl('/update');
                $_results = $this->queryRaw($client, $test, 'post', '/update', $params);
                return($_results);
            }
            if(property_exists($params, 'set'))
            {
                $_query->set($params->set);
            }
            if(property_exists($params, 'where'))
            {
                $_query->where($params->where);
            }
            echo "this should be it:\n";
            //var_dump($params['where']);
            $_results = $client->query($_query);
            $_results = $this->handleTestResult($test, $_results, $params);
            return($_results);
        }
    }

    public function execute($test, $client)
    {
        $_results = null;
        $compare = (string) $test->method;

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
        $_query = new Query();
        try
        {
            $client->query($_query->dropTable('test_dataset'));
            $client->query($_query->dropTable('new_test_dataset'));
            $client->query($_query->dropTable('45'));
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
            $_json = json_decode(file_get_contents($_file));
            array_push($tests, $_json);
            //var_dump($_json);
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
            foreach($tests[$i]->tests as $key => $test)
            {
                $client->setClientKey($test->parameters->key);
                $client->setClientSecret($test->parameters->secret);
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


    $secret = 'VCBA1hLyS2mYdrL6kO/iKQ==';
    $key = '7zNN1Pl9SQ6lNZwYe9mtQw==';
    $host = 'localhost';
    $port = 4001;
    $ssl = false;
    $testfile = "../../api-server/tests/test-suites.json";
    echo "testfile: ".realpath($testfile)."\n";
    $test_sequence = new datalancheTestSequence($secret, $key, $host, $port, $ssl, $testfile, null);
    $test_sequence->adventSequence();
?>