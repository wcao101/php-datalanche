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

        if($_secret !== null) {

            $this->deployed_client_parameters['secret'] = $_secret;
        }
        
        if($_key !== null) {

            $this->deployed_client_parameters['key'] = $_key;
        }
        
        if($_host !== null) {

            $this->deployed_client_parameters['host'] = $_host;
        }

        if($_port !== null) {

            $this->deployed_client_parameters['port'] = $_port;
        }

        if($_ssl !== null) {

            $this->deployed_client_parameters['ssl'] = $_ssl;
        }

        if($_test_suite !== null) {

            $this->deployed_client_parameters['test_suite'] = $_test_suite;

        } elseif ($_test_suite === null) {

            $this->deployed_client_parameters['test_suite'] = 'all';
        }

        if($_test_dataset_path !== null) {

            $this->deployed_client_parameters['test_dataset_path'] = $_test_dataset_path;

        } elseif ($_test_dataset_path === null) {

            throw new Exception("Unable to open dataset path, detected null entry. Now Exiting");

            exit();
        }

        return $this;
    }
    
    public function httpAssocEncode($array)
    {
        $requestOption = '';
        $arrayEnd = end($array);
        foreach($array as $key => $entry) {

            if($entry === null) {

                $entry = '';
            }
            
            if($entry === $arrayEnd) {

                $requestOption = (string) $requestOption.urlencode($key).'='.urlencode($entry);

            } else {

                $requestOption = (string) $requestOption.urlencode($key).'='.urlencode($entry).'&';
            }
        }

        $requestOption = (string) "?".$requestOption;

        return $requestOption;
    }

     public function rawCurlCreator()
    {
        //same as $client->curlCreator()
        $options = array (
            CURLOPT_HEADER => true,
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
            CURLINFO_HEADER_OUT => true
            );

        $curlHandle = curl_init();

        curl_setopt_array($curlHandle, $options);

        return $curlHandle;
    }

    public function startsWith ($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    public function endsWtih ($haystack, $needle)
    {
        $length = strlen($needle);

        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);   
    }

    public function isNotNull($r)
    {
        return !is_null($r);
    }

    public function scrobbleTestAuth($json, $relevantParameters)
    {
        for($i = 0; $i < count($json); $i++)
        {
            foreach($json[$i]->tests as $key => $test)
            {
                if($test->parameters->secret === 'valid_secret')
                {
                    $test->parameters->secret = $relevantParameters['secret'];
                }
                if($test->parameters->key === 'valid_key')
                {
                    $test->parameters->key = $relevantParameters['key'];
                }
                
            }  
        }

        return $json;
    }

    public function addTests($tests, $json)
    {
        foreach($json->tests as $indTest) {

            array_push($tests, $indTest);
        }

        return $tests;
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

    public function handleTestResult($test, $datalancheError, $data)
    {

        $result = 'FAIL';
        $message = null;
        $acutalResults = array (
            'status_code' => null,
            'exception' => null,
            'data' => null
            );

        if( ($test->expected->statusCode === $datalancheError['response']['http_status']) 
            && ($test->expected->exception == $datalancheError['data']['code'])
        ) {
            $result = 'PASS';
            echo $result.":".$test->name."\n";

            echo "EXP HTTP CODE: ".$test->expected->statusCode
                ." GOT HTTP CODE: ".$datalancheError['response']['http_status']
                ."\n";

            echo "EXP CONTENT CODE: ".$test->expected->exception
                ." GOT CONTENT CODE: ".$datalancheError['data']['code']
                ."\n";

            if( is_string($test->expected->data)
                && is_string($datalancheError['data']['message'])
            ) {

                echo "EXP MESSAGE: ".$test->expected->data
                    ." GOT MESSAGE: ".$datalancheError['data']['message']
                    ."\n";
            }

            echo "----------------------------------------\n";

            return true;

        } else {

            echo $result.":".$test->name."\n";

            echo "EXP HTTP CODE: ".$test->expected->statusCode
                ." GOT HTTP CODE: ".$datalancheError['response']['http_status']
                ."\n";

            echo "EXP CONTENT CODE: ".$test->expected->exception
                ." GOT CONTENT CODE: ".$datalancheError['data']['code']
                ."\n";

            if( is_string($test->expected->data) 
                && is_string($datalancheError['data']['message'])
            ) {

                echo "EXP MESSAGE: ".$test->expected->data
                    ." GOT MESSAGE: ".$datalancheError['data']['message']
                    ."\n";
            }

            echo "||||||||||||||TEST CONTENTS||||||||||||||||||\n";

            var_dump($test);

            echo "||||||||||||||||||END END||||||||||||||||||||\n";

            echo "----------------------------------------\n";

            if( ($test->name === "insert_into: values = one full row")
                || ($test->name === "drop_table: table_name = array")
                || ($test->name === "get_table_info: table_name = array")
            ) {

                echo "--------------\n";
                echo "THIS TEST HAS BEEN DEEMED TEMPORARILY ACCEPTABLE TO FAIL, PRESS ENTER TO CONTINUE..";
                $line = fgets(STDIN);
                unset($line);

            } else {

                echo "UNEXPECTED TEST FAILURE HAS BEEN DETECTED, EXITING...\n";
                exit();
            }

            return false;
        }
    }

    //ask what is up with this func
    public function useRawQuery($keys, $params)
    {

        $use_raw = false;

        foreach($params as $key => $param) {

            if( in_array($key, $keys) === false ) {

                echo "\nRAW QUERY BEING USED\n";
                echo "UNKNOWN VALUE: ";
                echo "[".$key."]\n";
                $use_raw = true;
                break;
            }
        }
        return $use_raw;
    }

    public function testResultsMediator($serverResponseString, $curlInfoArray)
    {
        $statusArray = array();
        $statusArray['request'] = array();
        $statusArray['response'] = array();
        $statusArray['request']['header'] = array();
        $statusArray['response']['header'] = array();
        $statusArray['response']['body'] = array();

        $responseHeader = substr($serverResponseString, 0, $curlInfoArray['header_size']);
        $responseBody = substr($serverResponseString, $curlInfoArray['header_size']);
        $responseHeader = explode("\n", $responseHeader);
        $statusArray['response']['header']['status'] = $responseHeader[0];

        array_shift($responseHeader);

        foreach($responseHeader as $value)
        {
            $middle = explode(":", $value);
            if(count($middle) <= 1)
            {
                /*
                * The explode function has returned an empty row result
                * which means that the current slot is porbably part of
                * the whitespace returned in the response. This happens when
                * the curl library appends the header is appended to the response string.
                * Therefore skip appending it to the content array
                * and move to the next value slot.
                */
            }else{

                $statusArray['response']['header'][trim($middle[0])] = trim($middle[1]);
            }
        }

        $responseBody = json_decode($responseBody, true);

        $statusArray['response']['body'] = $responseBody;
        $statusArray['response']['header']['http_code'] = $curlInfoArray['http_code'];

        $requestHeader = explode("\n", $curlInfoArray['request_header']);
        $statusArray['request']['header']['operation'] = explode(" ", $requestHeader[0]);
        $statusArray['request']['header']['url_parameters'] = $statusArray['request']['header']['operation'][1];
        $statusArray['request']['header']['http_version'] = $statusArray['request']['header']['operation'][2];
        $statusArray['request']['header']['operation'] = $statusArray['request']['header']['operation'][0];

        array_shift($requestHeader);

        foreach($requestHeader as $value)
        {
            $middle = explode(":", $value);
            if(count($middle) <= 1)
            {
                //the explode function has created a white-space entry
            }else{

                $statusArray['request']['header'][trim($middle[0])] = trim($middle[1]);
            }
        }
        $statusArray['curl_info_array'] = $curlInfoArray;

        return $statusArray;
    }

    public function testGetDebug($curlInfo, $curlExecResult)
    {
        $curlExecResultArray = $this->testResultsMediator($curlExecResult, $curlInfo);
        
        $debugObject = array(
                'request' => array (
                    'method' => $curlExecResultArray['request']['header']['operation'],
                    'url' => $curlExecResultArray['curl_info_array']['url'],
                    'headers' => $curlExecResultArray['request']['header']
                    ),
                'body' => $curlExecResultArray['request']['header']['url_parameters'],
                'response' => array (
                    'http_status' => $curlExecResultArray['response']['header']['http_code'],
                    'http_version' => $curlExecResultArray['request']['header']['http_version'],
                    'headers' => $curlExecResultArray['response']['header']
                    ),
                'data' => $curlExecResultArray['response']['body'],
                'curl_info_array' => $curlExecResultArray['curl_info_array']
            );

        return $debugObject;
    }

    public function executeRawQuery($curlHandle)
    {
        $curlExecResult = null; //variable for curl execution handle
        $curlInfo = null;
        $responseObject = null;

        $curlExecResult = curl_exec($curlHandle); // now actually making the only outside call  
        $curlInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);

        $responseObject = $this->testGetDebug($curlInfo, $curlExecResult);

        try {

            if(($curlInfo['http_code'] < 200)
                || ($curlInfo['http_code'] > 300)
            ) {
                throw new DLException($responseObject);
            }

        }catch(DLException $e) {

            echo "DL-ERROR: ".$e."\n";
        }


        return($responseObject);
    }

    //  WHAT IS $URL SUPPOSED TO BE? FULL URL?

    public function queryRaw($client, $test, $type, $url, $body) {

        $results = null;
        $pass = false;
        $authString = $client->getKey().":".$client->getSecret();
        $completeInfoArray = get_object_vars($test->parameters);
        $completeInfoArray['method'] = $type;
        $completeInfoArray['post_request_body'] = null;

        if($type === 'del') {
            //delete get request
            $requestUrl = $client->getClientUrl().$url.$this->httpAssocEncode($body);
            $curlHandle = $this->rawCurlCreator();
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($curlHandle, CURLOPT_USERPWD, $authString);
            curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);
            $results = $this->executeRawQuery($curlHandle);
            $results = $this->handleTestResult($test, $results, $body);

            return $results; 

        } elseif ($type === 'get') {
            //get request
            $requestUrl = $client->getClientUrl().$url.$this->httpAssocEncode($body);
            $curlHandle = $this->rawCurlCreator();
            curl_setopt($curlHandle, CURLOPT_USERPWD, $authString);
            curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);
            $results = $this->executeRawQuery($curlHandle);
            $results = $this->handleTestResult($test, $results, $body);

            return $results;

        } elseif ($type === 'post') {
            //post request
            $completeInfoArray['post_request_body'] = $body;
            $requestUrl = $client->getClientUrl().$url;
            $curlHandle = $this->rawCurlCreator();

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
            
            curl_setopt($curlHandle, CURLOPT_USERPWD, $authString);
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type application/json', 'Content-Length: '.strlen($body)));
            curl_setopt($curlHandle, CURLOPT_POST, true);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $body);
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);

            $results = $this->executeRawQuery($curlHandle);
            $results = $this->handleTestResult($test, $results, $body);

            return $results;
        }
    }

    public function alterTable($test, $client)
    {
        $results = null;
        $params = $test->parameters;
        $keys = array (
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

        $useRaw = $this->useRawQuery($keys, $params);

        if( ($useRaw === true) )
        {
            unset($params->key); 
            unset($params->secret);

            $results = $this->queryRaw($client, $test, 'post', '/alter_table', $params);

            return($results);

        } else {

            $query = new Query();
            unset($params->key); 
            unset($params->secret);

            if( (property_exists($params, 'table_name') === true) ) {

                $query->alterTable($params->table_name);

            } else {

                $query->setMethod('post');
                $query->setBaseUrl('/alter_table');
            }

            if(property_exists($params, 'rename')) {

                $query->rename($params->rename);

            } elseif (property_exists($params, 'description')) {

                $query->description($params->description);

            } elseif (property_exists($params, 'is_private')) {

                $query->isPrivate($params->is_private);

            } elseif (property_exists($params, 'license')) {

                $query->license($params->license);

            } elseif (property_exists($params, 'sources')) {

                $query->sources($params->sources);
            }

            if( (property_exists($params, 'add_columns'))
                && (is_array($params->add_columns) === true)
            ) {

                for($i = 0; $i < count($params->add_columns); $i++) {

                    $query->addColumn($params->add_columns[$i]);
                }

            } elseif ( (property_exists($params, 'add_columns')) ) {

                $results = $this->queryRaw($client, $test, 'post', '/alter_table', $params);

                return $results;
            }

            if( (property_exists($params, 'drop_columns')) 
                && (is_array($params->drop_columns) === true) 
            ) {

                for($i = 0; $i < count($params->drop_columns); $i++) {

                    $query->dropColumn($params->drop_columns[$i]);
                }

            } elseif(property_exists($params, 'drop_columns')) {

                $results = $this->queryRaw($client, $test, 'post', '/alter_table', $params);

                return $results;
            }

            if( (property_exists($params, 'alter_columns')) 
                && (is_object($params->alter_columns) === true) 
                && (count(get_object_vars($params)) > 0)
            ) {

                foreach($params->alter_columns as $key => $value) {

                    $query->alterColumn($key, $value);
                }

            } elseif( (property_exists($params, 'alter_columns')) ) {

                $results = $this->queryRaw($client, $test, 'post', '/alter_table', $params);

                return $results;
            }

            $results = $client->query($query);
            $results = $this->handleTestResult($test, $results, $params);

            return $results;
        }
    }

    public function createTable($test, $client)
    {

        $params = $test->parameters;
        $useRaw = null;
        $results = null;
        $query = null;
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

        $useRaw = $this->useRawQuery($keys, $params);
        //echo $use_raw."\n";
        //exit();

        if($useRaw === true) {

            unset($params->key); 
            unset($params->secret);

            $results = $this->queryRaw($client, $test, 'post', '/create_table', $params);

            return $results;

        } else {

            $query = new Query();
            unset($params->key); 
            unset($params->secret);

            if( (isset($params->table_name)) ) {

                $query->createTable($params->table_name);

            }else{

                if( (property_exists($params, 'table_name')) ) {

                    $query->createTable($params->table_name);

                } else {

                    $query->setMethod('post');
                    $query->setBaseUrl('/create_table');
                }
            }

            if( (property_exists($params, 'description')) ) {

                $query->description($params->description);
            }

            if( (property_exists($params, 'is_private')) ) {

                $query->isPrivate($params->is_private);
            }

            if( (property_exists($params, 'license')) ) {

                $query->license($params->license);
            }

            if( (property_exists($params, 'sources')) ) {

                $query->sources($params->sources);
            }

            if( (property_exists($params, 'columns')) ) {

                $query->columns($params->columns);
            }
            
            $results = $client->query($query);
            $results = $this->handleTestResult($test, $results, $params);

            return $results;
        }
    }

    public function dropTable($test, $client)
    {
        $useRaw = null;
        $results = null;
        $query = null;
        $params = $test->parameters;
        $keys = array( 'table_name', 'key', 'secret' );

        $useRaw = $this->useRawQuery($keys, $params);

        if( ($useRaw === true) ) {

            unset($params->key); 
            unset($params->secret);

            $results = $this->queryRaw($client, $test, 'del', '/drop_table', $params);

            return $results;

        } else {

            $query = new Query();

            unset($params->key); 
            unset($params->secret);

            if( (property_exists($params, 'table_name')) ) {

                if( (is_bool($params->table_name)) ) {

                    if( ($params->table_name === true) ) {

                        $params->table_name = "true";

                    } elseif ( ($params->table_name === false) ) {

                        $params->table_name = "false";
                    }
                }

                $query->dropTable($params->table_name);

            } else {

                $query->setMethod('post');
                $query->setBaseUrl('/drop_table');
                $results = $this->queryRaw($client, $test, 'del', '/drop_table', $params);

                return $results;
            }

            $results = $client->query($query);
            $results = $this->handleTestResult($test, $results, $params);

            return $results;
        }
    }

    public function deleteFrom($test, $client)
    {
        $results = null;
        $query = null;
        $useRaw = null;
        $params = $test->parameters;
        $keys = array( 'table_name', 'where', 'key', 'secret' );

        $useRaw = $this->useRawQuery($keys, $params);

        if($useRaw === true) {

            unset($params->key); 
            unset($params->secret);

            $results = $this->queryRaw($client, $test, 'post', '/delete_from', $params);

            return $results;

        } else {

            $query = new Query();

            if( (property_exists($params, 'table_name')) ) {

                $query->deleteFrom($params->table_name);

            }else {

                $query->setMethod('post');
                $query->setBaseUrl('/delete_from');
                $results = $this->queryRaw($client, $test, 'post', '/delete_from', $params);

                return $results;
            }

            if(property_exists($params, 'where')) {

                $query->deleteFrom($params->table_name)->where($params->where);
            }
            //echo "this should be it\n";
            //var_dump($params['where']);
            $results = $client->query($query);
            $results = $this->handleTestResult($test, $results, $params);

            return $results;
        }
    }

    public function getTableList($test, $client)
    {
        $results = null;
        $query = null;
        $useRaw = null;
        $params = $test->parameters;
        $keys = array('key', 'secret');

        $useRaw = $this->useRawQuery($keys, $params);

        if( ($useRaw === true) ) {

            unset($params->key); 
            unset($params->secret);

            $results = $this->queryRaw($client, $test, 'get', '/get_table_list', $params);

            return $results;

        } else {       

            if($test->expected === 200) { 

                $query = new Query();

            } else {

                    $query= new Query();
                }

                unset($params->key); 
                unset($params->secret);
                $query->getTableList();
                $results = $client->query($query);
            
            
            $results = $this->handleTestResult($test, $results, $params);

            return $results;
        }
    }

    public function getTableInfo($test, $client)
    {
        $query = null;
        $results = null;
        $useRaw = null;
        $params = $test->parameters;
        $keys = array( 'table_name', 'key', 'secret' );

        $useRaw = $this->useRawQuery($keys, $params);
        
        if($useRaw === true)
        {
            unset($params->key); 
            unset($params->secret);
            $results = $this->queryRaw($client, $test, 'get', '/get_table_info', $params);

            return $results;
        } else {

            $query = new Query();
            unset($params->key); 
            unset($params->secret);

            if( (property_exists($params, 'table_name') === true) ) {

                if($params->table_name === null) {

                    $results = $this->queryRaw($client, $test, 'get', '/get_table_info', $params);

                    return $results;

                } else {

                    if(is_bool($params->table_name)) {

                        if($params->table_name === true) {

                            $params->table_name = "true";

                        }elseif($params->table_name === false) {

                            $params->table_name = "false";
                        } 
                    }

                    $query->getTableInfo($params->table_name);
                }
            } else {

                $results = $this->queryRaw($client, $test, 'get', '/get_table_info', $params);

                return $results;
            }

            $results = $client->query($query);
            $results = $this->handleTestResult($test, $results, $params);

            return $results;
        }
    }

    public function insertInto($test, $client)
    {
        $results = null;
        $path = null;
        $query = null;
        $rows = null;
        $useRaw = null;
        $params = $test->parameters;
        $keys = array( 'table_name', 'values', 'key', 'secret' );

        $useRaw = $this->useRawQuery($keys, $params);

        if($useRaw === true) {

            unset($params->key); 
            unset($params->secret);
            $results = $this->queryRaw($client, $test, 'post', '/insert_into', $params);

            return $results;

        }else {

            $query = new Query();
            unset($params->key); 
            unset($params->secret);

            if(property_exists($params, 'table_name')) {

                $query->insertInto($params->table_name);

            } else {

                $query->setMethod('post');
                $query->setBaseUrl('/insert_into');
                $results = $this->queryRaw($client, $test, 'post', '/insert_into', $params);

                return $results;
            }

            if($params->values === 'dataset_file') {

                $path = realpath("../../api-server/tests/".$test->dataset_file);
                $rows = $this->getRecordsFromFile($path);
                $query->values($rows);
                $results = $client->query($query);
                $results = $this->handleTestResult($test, $results, $params);

                return $results;

            } else {

                if(property_exists($params, 'values')) {

                    $query->values($params->values);
                }

                $results = $client->query($query);
                $results = $this->handleTestResult($test, $results, $params);

                return $results;
            }
        }
    }

    public function selectFrom($test, $client)
    {
        $useRaw = null;
        $results = null;
        $query = null;
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

        $useRaw = $this->useRawQuery($keys, $params);

        if($useRaw === true)
        {
            unset($params->key); 
            unset($params->secret);

            $results = $this->queryRaw($client, $test, 'post', '/select_from', $params);

            return $results;

        }else {
            $query = new Query();
            unset($params->key); 
            unset($params->secret);

            if(property_exists($params, 'select')) {

                $query->select($params->select);

            } else {

                $query->setMethod('post');
                $query->setBaseUrl('/select_from');
                $results = $this->queryRaw($client, $test, 'post', '/select_from', $params);
                return $results;
            }

            if ( (property_exists($params, 'distinct')) ) {

                $query->distinct($params->distinct);
            }

            if ( (property_exists($params, 'from')) ) {

                $query->from($params->from);
            }

            if ( (property_exists($params, 'where')) ) {

                $query->where($params->where);
            }

            if ( (property_exists($params, 'group_by')) ) {

                $query->groupBy($params->group_by);
            }

            if ( (property_exists($params, 'order_by')) ) {

                $query->orderBy($params->order_by);
            }

            if ( (property_exists($params, 'offset')) ) {

                $query->offset($params->offset);
            }

            if ( (property_exists($params, 'limit')) ) {

                $query->limit($params->limit);
            }

            if ( (property_exists($params, 'total')) ) {

                $query->total($params->total);
            }

            $results = $client->query($query);
            $results = $this->handleTestResult($test, $results, $params);
            
            return $results;
        }
    }

    public function update($test, $client)
    {
        $useRaw = null;
        $results = null;
        $query = null;
        $params = $test->parameters;
        $keys = array ('table_name', 'set', 'where', 'key', 'secret');

        $useRaw = $this->useRawQuery($keys, $params);

        if($useRaw === true)
        {
            unset($params->key); 
            unset($params->secret);
            $results = $this->queryRaw($client, $test, 'post', '/update', $params);

            return $results;

        } else {

            $query = new Query();

            if(property_exists($params, 'table_name')) {

                $query->update($params->table_name);

            } else {

                $query->setMethod('post');
                $query->setBaseUrl('/update');
                $results = $this->queryRaw($client, $test, 'post', '/update', $params);

                return $results;
            }

            if(property_exists($params, 'set')) {

                $query->set($params->set);
            }
            
            if(property_exists($params, 'where')) {

                $query->where($params->where);
            }

            $results = $client->query($query);
            $results = $this->handleTestResult($test, $results, $params);
            return $results;
        }
    }

    public function execute($test, $client)
    {
        $results = null;

        $compare = (string) $test->method;

        if($compare === 'alter_table') {

            $results = $this->alterTable($test, $client);

        } elseif ($compare === 'create_table') {

            $results = $this->createTable($test, $client);

        } elseif($compare === 'delete_from') {

            $results = $this->deleteFrom($test, $client);

        } elseif($compare === 'drop_table') {

            $results = $this->dropTable($test, $client);

        } elseif($compare === 'get_table_info') {

            $results = $this->getTableInfo($test, $client);

        } elseif($compare === 'get_table_list') {

            $results = $this->getTableList($test, $client);

        } elseif($compare === 'insert_into') {

            $results = $this->insertInto($test, $client);

        } elseif($compare === 'select_from') {

            $results = $this->selectFrom($test, $client);

        } elseif($compare === 'update') {

            $results = $this->update($test, $client);

        } else {

            var_dump($test);
            echo "PERSCRIBED TEST METHOD: ".$compare."\n";
            throw new Exception("datalancheTestSequence->execute() has reported being out of focus. [FATAL]\n");
            exit();
        }

        return $results;
    }

    public function cleanSets($client)
    {
        $query = new Query();

        try {

            $client->query($query->dropTable('test_dataset'));
            $client->query($query->dropTable('new_test_dataset'));
            $client->query($query->dropTable('45'));

        } catch(Exception $e) {
            echo $e."\n";
        }
    }

    public function adventSequence()
    {
        $tests = array();
        $file = null;

        $exParam = array(
            'key' => $this->deployed_client_parameters['key'],
            'secret' => $this->deployed_client_parameters['secret']
            );

        $json = null;
        $results = null;
        $numberTestsPassed = null;
        $datasetPath = $this->deployed_client_parameters['test_dataset_path'];
        $chosenSuite = $this->deployed_client_parameters['test_suite'];

        $contents = json_decode(file_get_contents($datasetPath), true);

        $rootDir = realpath(dirname($datasetPath));

        $subTestPath = $rootDir.'/'.$contents['dataset_file'];

        for($i = 0; $i < count($contents['suites'][$chosenSuite]); $i++) {

            $file = $rootDir.'/'.$contents['suites'][$chosenSuite][$i];

            $json = json_decode(file_get_contents($file));

            array_push($tests, $json);
        }

        $client = new Client($this->deployed_client_parameters['secret'],
                             $this->deployed_client_parameters['key'],
                             $this->deployed_client_parameters['host'],
                             $this->deployed_client_parameters['port'],
                             $this->deployed_client_parameters['ssl']);

        $this->cleanSets($client);

        $tests = $this->scrobbleTestAuth($tests, $exParam);

        for($i = 0; $i < count($tests); $i++) {

            foreach($tests[$i]->tests as $key => $test) {

                $client->setKey($test->parameters->key);
                $client->setSecret($test->parameters->secret);
                $results = $this->execute($test, $client);

                if($results)
                {
                    $numberTestsPassed++;
                }
            }
        }
        echo "\n------\n # of tests reporting success: "
            .$numberTestsPassed
            ."\n------\n";

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