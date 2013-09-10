<?php
/*
* client.php
* created by: cristian cavalli
* property of: Datalanche Inc.
* purpose: allow interaction between php clients and
* dalanche services
* target: datalanche
*/
/* DEPENDICES */
include 'query.php';
include 'expression.php';
//include 'DLException.php';

class Client 
{
    private $_parameters;
    private $_key;
    private $_secret;
    private $_url;
    private $_verifySsl;
    private $_connection;
    private $_getUrl;
    private $_host;
    private $_curlStatusArray;


    public function __construct ($secret, $key, $host, $port, $ssl)
    {
        //assignments
        $url = 'https://';
        $this->_curlStatusArray = array();

        if($host === null || $host === '')
        {
            $this->_host = 'api.datalanche.com';
        }else{
            $this->_host = $host;
        }

        $url = $url.$host;

        if ($port === null || $port === '')
        {
            $this->_port = null;
        }else{
            $this->_port = $port;
            $url = $url.':'.$port;
        }

        $this->_url = $url;

        if ($ssl === false || $ssl === 0 || $ssl === 'false'){
            $this->verify_ssl = $ssl;
        }else if($ssl === null || $ssl === true || $ssl === 1 || $ssl === 'true'){
            $this->_verifySsl = true;
        }

        if($secret != null)
        {
            $this->_secret = $secret; 
        }
        if ($key != null)
        {
            $this->_key = $key;
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
            if($entry == $end)
            {
                $requestOption = (string) $requestOption.urlencode($key).'='.urlencode($entry);
            } else {
                $requestOption = (string) $requestOption.urlencode($key).'='.urlencode($entry).'&';
            }
        }
        return($requestOption);
    }

    public function curlCreator()
    {
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

    public function getBody($query)
    {
        
        $queryParameters = $query->getParameters();
        $queryBaseUrl = $query->getBaseUrl();
        $postRequestBody = array();
        echo "GET BODY:\n";
        //var_dump($query->getParameters());
        echo "000000000000\n";

        if($query === null){
            throw new Exception("The query for function getBody() was null\n");
            return($requestBody);
        }


        if($queryBaseUrl === '/alter_table')
        {
            if(array_key_exists('add_columns', $queryParameters))
            {
                //echo "sdkfjskdfjhksdjhfsd\n";
                $postRequestBody['add_columns'] = $queryParameters['add_columns'];
            }
            if(array_key_exists('alter_columns', $queryParameters))
            {
                $postRequestBody['alter_columns'] = $queryParameters['alter_columns'];
            }
            if(array_key_exists('table_name', $queryParameters))
            {
                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }
            if(array_key_exists('description', $queryParameters))
            {
                $postRequestBody['description'] = $queryParameters['description'];
            }
            if(array_key_exists('drop_columns', $queryParameters))
            {
                $postRequestBody['drop_columns'] = $queryParameters['drop_columns'];
            }
            if(array_key_exists('is_private', $queryParameters))
            {
                $postRequestBody['is_private'] = $queryParameters['is_private'];
            }
            if(array_key_exists('license', $queryParameters))
            {
                $postRequestBody['license'] = $queryParameters['license'];
            }
            if(array_key_exists('rename', $queryParameters))
            {
                $postRequestBody['rename'] = $queryParameters['rename'];
            }
            if(array_key_exists('sources', $queryParameters))
            {
                $postRequestBody['sources'] = $queryParameters['sources'];
            }

        }

        elseif($queryBaseUrl === '/create_table')
        {
            if(array_key_exists('columns', $queryParameters))
            {
                $postRequestBody['columns'] = $queryParameters['columns'];
            }
            if(array_key_exists('table_name', $queryParameters))
            {
                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }
            if(array_key_exists('description', $queryParameters))
            {
                $postRequestBody['description'] = $queryParameters['description'];
            }
            if(array_key_exists('is_private', $queryParameters))
            {
                $postRequestBody['is_private'] = $queryParameters['is_private'];
            }
            if(array_key_exists('license', $queryParameters))
            {
                $postRequestBody['license'] = $queryParameters['license'];
            }
            if(array_key_exists('sources', $queryParameters))
            {
                $postRequestBody['sources'] = $queryParameters['sources'];
            }
        }

        elseif ($queryBaseUrl === '/delete_from')
        {
            if(array_key_exists('table_name', $queryParameters))
            {
                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }
            if(array_key_exists('where', $queryParameters))
            {
                $postRequestBody['where'] = $queryParameters['where'];
            }
        }

        elseif ($queryBaseUrl === '/insert_into')
        {
            if(array_key_exists('table_name', $queryParameters))
            {
                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }
            if(array_key_exists('values', $queryParameters))
            {
                $postRequestBody['values'] = $queryParameters['values'];
            }
        }

        elseif ($queryBaseUrl === '/select_from')
        {
            if(array_key_exists('table_name', $queryParameters))
            {
                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }
            if(array_key_exists('distinct', $queryParameters))
            {
                $postRequestBody['distinct'] = $queryParameters['distinct'];
            }
            if(array_key_exists('from', $queryParameters))
            {
                $postRequestBody['from'] = $queryParameters['from'];
            }
            if(array_key_exists('group_by', $queryParameters))
            {
                $postRequestBody['group_by'] = $queryParameters['group_by'];
            }
            if(array_key_exists('limit', $queryParameters))
            {
                $postRequestBody['limit'] = $queryParameters['limit'];
            }
            if(array_key_exists('offset', $queryParameters))
            {
                $postRequestBody['offset'] = $queryParameters['offset'];
            }
            if(array_key_exists('order_by', $queryParameters))
            {
                $postRequestBody['order_by'] = $queryParameters['order_by'];
            }
            if(array_key_exists('select', $queryParameters))
            {
                $postRequestBody['select'] = $queryParameters['select'];
            }
            if(array_key_exists('total', $queryParameters))
            {
                $postRequestBody['total'] = $queryParameters['total'];
            }
            if(array_key_exists('where', $queryParameters))
            {
                $postRequestBody['where'] = $queryParameters['where'];
            }
        }

        elseif($queryBaseUrl === '/update')
        {
            if(array_key_exists('table_name', $queryParameters))
            {
                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }
            if(array_key_exists('set', $queryParameters))
            {
                $postRequestBody['set'] = $queryParameters['set'];
            }
            if(array_key_exists('where', $queryParameters))
            {
                $postRequestBody['where'] = $queryParameters['where'];
            }
        }
        else
        {
            var_dump($query);
            throw new Exception("Out of range on get_body...\n exiting\n");
            exit();
        }

        //$postRequestBody = array_filter($postRequestBody, '!is_null');
        if(count($postRequestBody) === 0)
        {
            $postRequestBody = new stdClass();
        }

        return($postRequestBody);
    }

    public function getUrl($query)
    {
        if($query === null)
        {
            return('/');
        }

        $queryString = null;
        $queryParameters = $query->getParameters();
        $queryBaseUrl = $query->getBaseUrl();

        $getParameters = array();

        if($queryBaseUrl === '/drop_table')
        {
            if($queryParameters['table_name'])
            {
                $getParameters['table_name'] = $queryParameters['table_name'];
            }elseif(!$queryParameters['table_name']){
                $getParameters['table_name'] = null;
            }
        }

        elseif($queryBaseUrl === '/get_table_info')
        {
            if($queryParameters['table_name'])
            {
                $getParameters['table_name'] = $queryParameters['table_name'];
            }elseif(!$queryParameters['table_name']){
                $getParameters['table_name'] = null;
            }
        }

        elseif($queryBaseUrl === '/get_table_list')
        {
            $queryBaseUrl = $this->_url.$queryBaseUrl;
            echo "get table list found: ".$queryBaseUrl."\n";
            return($queryBaseUrl);
        }

        $queryString = $this->httpAssocEncode($getParameters);
        echo "QUERY: ".$queryString."\n";

        if($queryString !== null)
        {
            $queryBaseUrl = $this->_url.$queryBaseUrl.$queryString;        
        }

        return($queryBaseUrl);
    }

    public function query($query)
    {
        $results = null;
        $queryMethodType = $query->getMethodType();



        if(!$query)
        {
            throw new Exception("Query was null in client->query()\n");
            exit();
        }

        if($queryMethodType === 'del')
        {
            $results = $this->clientDelete($query);
            return($results);
        }
        if($queryMethodType === 'get')
        {
            //echo "sdkhjfklsdhfsdjkhfjklsdhfdsjf\n\n";
            $results = $this->clientGet($query);
            return($results);
        }
        if($queryMethodType === 'post')
        {
            $results = $this->clientPost($query);
            return($results);
        }
    }

    public function clientPost($query)
    {
        $key = $this->_key;
        $secret = $this->_secret;
        $results = null;
        $postRequestBody = null;
        $httpAuthString = null;
        $requestUrl = null;
        $curlHandle = null;
        

        /*

        if($query->test_key !== null)
        {
            $_key = $query->test_key;
        }else {
            $_key = $this->key;
        }

        if($query->test_secret !== null)
        {
            $_secret = $query->test_secret;
        }else {
            $_secret = $this->secret;
        }
        */

        $postRequestBody = $this->getBody($query);
        $httpAuthString = (string) $key.":".$secret;
        /*
        echo "\n-----AUTH-----\n";
        echo "string: ".$httpAuthString."\n";
        echo "key ".$key."\n";
        echo "secret ".$secret."\n";
        echo "\n------------\n";
        */
        $requestUrl = $this->getUrl($query);
        echo "POST URL FORMULATED: ".$requestUrl."\n";
        $curlHandle = $this->curlCreator();
        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_USERPWD, $httpAuthString);
        //var_dump($postRequestBody);
        echo "HERE IS THE ENCODED BODY:\n";
        ///var_dump(json_encode($postRequestBody));
        //echo "\n----------------------\n";
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($postRequestBody));
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);
        /*
        echo "\n----------------------------------------------\n";
        echo "query::\n";
        //var_dump(json_encode($postRequestBody));
        echo "\n----------------------------------------------\n";
        */
        $results = $this->handleResults($curlHandle);

        return($results);
    }

    public function clientGet($query)
    {
        $key = $this->_key;
        $secret = $this->_secret;
        $results = null;
        $httpAuthString = null;
        $requestUrl = null;
        $curlHandle = null;

        ///test for subbed key
        /*
        if($query->test_key !== null)
        {
            $_key = $query->test_key;
        }else {
            $_key = $this->key;
        }
        ///test for subbed secret
        if($query->test_secret !== null)
        {
            $_secret = $query->test_secret;
        }else {
            $_secret = $this->secret;
        }
        */

        $httpAuthString = (string) $key.":".$secret;
        $requestUrl = $this->getUrl($query);
        $curlHandle = $this->curlCreator();
        curl_setopt($curlHandle, CURLOPT_USERPWD, $httpAuthString);
        curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);
        $results = $this->handleResults($curlHandle);

        return($results);
    }

    public function clientDelete($query)
    {
        $key = $this->_key;
        $secret = $this->_secret;
        $results = null;
        $httpAuthString = null;
        $requestUrl = null;
        $curlHandle = null;

        /*
        if($query->test_key !== null)
        {
             $_key = $query->test_key;
        }else {
             $_key = $this->key;
        }
        if($query->test_secret !== null)
        {
            $_secret = $query->test_secret;
        }else {
            $_secret = $this->secret;
        }
        */

        //now creating the specific authstring
        $httpAuthString = (string) $key.":".$secret;
        //setting url and curl_request handle
        $requestUrl = $this->getUrl($query);
        $curlHandle = $this->curlCreator();

        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curlHandle, CURLOPT_USERPWD, $httpAuthString);
        curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);
        $results = $this->handleResults($curlHandle);

        return($results);
    }

    public function handleResults($curlHandle)
    {
        //handle results gets curl handle, executes it, and
        //then returns pertinant results about the interaction
        $curlInteraction = null; //variable for curl execution handle
        $lastCurlError = null; //variable for curl end session error output
        $curlDebug = null;
        $curlInfo = null;
        $transactionErrors = null;
        try
        {
            $curlInteraction = curl_exec($curlHandle); // now actually making the only outside call
            $curlInfo = curl_getinfo($curlHandle);
            //var_dump($curlInfo);
            //echo "-------------CURL RESPONSE-----------------\n";
            //var_dump($curlInfo);
            //var_dump($curlInteraction);
            //echo "-------------END END END-----------------\n";
            curl_close($curlHandle);
            $transactionErrors = array (
                'statusCode' => $curlInfo['http_code'],
                );
            if($transactionErrors['statusCode'] === null || $transactionErrors['statusCode'] === '')
            {

            }
                /*
                echo "++++++++++++++++++++++++++++++++++++++++++++\n";
                echo "GOT CODE: ".$transaction_errors['statusCode']."\n";
                echo "CURL INFO DUMP:\n";
                var_dump($curl_debug);
                echo "++++++++++++++++++++++++++++++++++++++++++++\n";
                */
           // echo "LAST CURL ERROR: ".$curl_info."\n";
        } catch (Exception $e) {
            echo "curl error: ".$e;
        }

        if($lastCurlError){
            array_push($this->_curlStatusArray, json_decode($lastCurlError));
        }

        return($transactionErrors);
    }

    public function setClientKey($key)
    {
        $this->_key = $key;
    }

    public function setClientSecret($secret)
    {
        $this->_secret = $secret;
    }

    public function setClientHost($host)
    {
        $this->_host = $host;
    }

    public function setClientPort($port)
    {
        $this->_post = $port;
    }

    public function setClientSsl($boolSsl)
    {
        $this->_verifySsl = $boolSsl;
    }

    public function isNotNull($mixedVar)
    {
        return !is_null($mixedVar);
    }

    public function getClientKey()
    {
        return($this->_key);
    }

    public function getClientSecret()
    {
        return($this->_secret);
    }

    public function getClientUrl()
    {
        return($this->_url);
    }

    public function getDebugInfo($curlHandle, $results, $query)
    {
        $curlInfo = curl_getinfo($curlHandle);
        $debugObject = array(
                'request' => array (
                    'method' => $query->getMethodType(),
                    'url' => $curlInfo['url'],
                    'headers' => $curlInfo['request_header']
                    ),
                'response' => array (
                    'http_status' => $results['statusCode'],
                    'http_version' => $results['httpVersion'],
                    'headers' => $results['headers']
                    )
            );

        return($debugObject);
    }
}

?>