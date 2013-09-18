<?php
/*
* DLClient.php
* created by: cristian cavalli
* property of: Datalanche Inc.
* purpose: allow interaction between php clients and
* dalanche services
* target: datalanche
* operation: parses php native data typers into propreitary
* json format for datalanche and then mediates responses from
* datalanche services.
*/
/* DEPENDICES */
include 'DLQuery.php';
include 'DLExpression.php';
include 'DLException.php';


/*
\
*Class DLClient contains the overwhelming amount of functionality
*regarding connection creation, control and closure.
/
*/
class DLClient 
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

        if(($host === null)
            || ($host === '')
        ) {

            $this->_host = 'api.datalanche.com';

        } else {

            $this->_host = $host;

        }

        $url = $url.$host;

        if (($port === null)
            || ($port === '')
        ) {

            $this->_port = null;

        } else {

            $this->_port = $port;
            $url = $url.':'.$port;

        }

        $this->_url = $url;

        if (($ssl === false)
            || ($ssl === 0)
            || ($ssl === 'false')
        ) {

            $this->verify_ssl = $ssl;

        } elseif(($ssl === null)
            || ($ssl === true) 
            || ($ssl === 1) 
            || ($ssl === 'true')
        ) {

            $this->_verifySsl = true;
        }

        if($secret != null) {

            $this->_secret = $secret; 

        }
        if ($key != null) {

            $this->_key = $key;

        }

        return $this;
    }

    private function httpAssocEncode($array)
    {
        $requestOption = '';
        $end = end($array);

        foreach($array as $key => $entry) {

            if($entry === null) {

                $entry = '';
            }
            
            if($entry == $end) {

                $requestOption = (string) $requestOption.urlencode($key).'='.urlencode($entry);

            } else {

                $requestOption = (string) $requestOption.urlencode($key).'='.urlencode($entry).'&';
            }

        }

        if($requestOption !== ''){

            $requestOption = (string) "?".$requestOption;
        }

        return $requestOption;
    }

    private function curlCreator()
    {
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

    private function resultsMediator($serverResponseString, $curlInfoArray)
    {
        //var_dump($serverResponseString);
        //var_dump($curlInfoArray);
        //exit();
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

        switch( json_last_error() ) {
            case JSON_ERROR_DEPTH:
                throw new Exception('JSON error: Maximum stack depth has been exceeded');
                break;

            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception('JSON error: Underflow or the modes mismatch');
                break;

            case JSON_ERROR_CTRL_CHAR:
                throw new Exception('JSON error: unexpected ctrl charachter');
                break;

            case JSON_ERROR_SYNTAX:
                throw new Exception('JSON error: syntax error, malformed JSON detected');
                break;

            case JSON_ERROR_UTF8:
                throw new Exception('JSON error: Malformed UTF_* charachters, possibly incorrectly encoded');
                break;
            case JSON_ERROR_NONE:
            default:
                break;
        }

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

    private function getBody($query)
    {
        
        $queryParameters = $query->getParameters();
        $queryBaseUrl = $query->getBaseUrl();
        $postRequestBody = array();

        if($query === null) {

            throw new Exception("The query for function getBody() was null\n");

            return $requestBody;
        }


        if($queryBaseUrl === '/alter_table') {

            if(array_key_exists('add_columns', $queryParameters)) {
                $postRequestBody['add_columns'] = $queryParameters['add_columns'];
            }

            if(array_key_exists('alter_columns', $queryParameters)) {

                $postRequestBody['alter_columns'] = $queryParameters['alter_columns'];
            }

            if(array_key_exists('table_name', $queryParameters)) {

                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }

            if(array_key_exists('description', $queryParameters)) {

                $postRequestBody['description'] = $queryParameters['description'];
            }

            if(array_key_exists('drop_columns', $queryParameters)) {

                $postRequestBody['drop_columns'] = $queryParameters['drop_columns'];
            }

            if(array_key_exists('is_private', $queryParameters)) {

                $postRequestBody['is_private'] = $queryParameters['is_private'];
            }

            if(array_key_exists('license', $queryParameters)) {

                $postRequestBody['license'] = $queryParameters['license'];
            }

            if(array_key_exists('rename', $queryParameters)) {

                $postRequestBody['rename'] = $queryParameters['rename'];
            }

            if(array_key_exists('sources', $queryParameters)) {

                $postRequestBody['sources'] = $queryParameters['sources'];
            }

        } elseif($queryBaseUrl === '/create_table') {

            if(array_key_exists('columns', $queryParameters)) {

                $postRequestBody['columns'] = $queryParameters['columns'];
            }

            if(array_key_exists('table_name', $queryParameters)) {

                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }

            if(array_key_exists('description', $queryParameters)) {

                $postRequestBody['description'] = $queryParameters['description'];
            }

            if(array_key_exists('is_private', $queryParameters)) {

                $postRequestBody['is_private'] = $queryParameters['is_private'];
            }

            if(array_key_exists('license', $queryParameters)) {

                $postRequestBody['license'] = $queryParameters['license'];
            }

            if(array_key_exists('sources', $queryParameters)) {

                $postRequestBody['sources'] = $queryParameters['sources'];
            }
        } elseif ($queryBaseUrl === '/delete_from') {

            if(array_key_exists('table_name', $queryParameters)) {

                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }

            if(array_key_exists('where', $queryParameters)) {

                $postRequestBody['where'] = $queryParameters['where'];
            }
        } elseif ($queryBaseUrl === '/insert_into') {

            if(array_key_exists('table_name', $queryParameters)) {

                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }

            if(array_key_exists('values', $queryParameters)) {

                $postRequestBody['values'] = $queryParameters['values'];
            }
        } elseif ($queryBaseUrl === '/select_from') {

            if(array_key_exists('table_name', $queryParameters)) {

                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }

            if(array_key_exists('distinct', $queryParameters)) {

                $postRequestBody['distinct'] = $queryParameters['distinct'];
            }
            
            if(array_key_exists('from', $queryParameters)) {

                $postRequestBody['from'] = $queryParameters['from'];
            }
            
            if(array_key_exists('group_by', $queryParameters)) {

                $postRequestBody['group_by'] = $queryParameters['group_by'];
            }
            
            if(array_key_exists('limit', $queryParameters)) {

                $postRequestBody['limit'] = $queryParameters['limit'];
            }

            if(array_key_exists('offset', $queryParameters)) {

                $postRequestBody['offset'] = $queryParameters['offset'];
            }
            
            if(array_key_exists('order_by', $queryParameters)) {

                $postRequestBody['order_by'] = $queryParameters['order_by'];
            }

            if(array_key_exists('select', $queryParameters)) {

                $postRequestBody['select'] = $queryParameters['select'];
            }
            
            if(array_key_exists('total', $queryParameters)) {

                $postRequestBody['total'] = $queryParameters['total'];
            }

            if(array_key_exists('where', $queryParameters)) {
                $postRequestBody['where'] = $queryParameters['where'];
            }
        } elseif($queryBaseUrl === '/update') {

            if(array_key_exists('table_name', $queryParameters)) {
                $postRequestBody['table_name'] = $queryParameters['table_name'];
            }

            if(array_key_exists('set', $queryParameters)) {
                $postRequestBody['set'] = $queryParameters['set'];
            }

            if(array_key_exists('where', $queryParameters)) {
                $postRequestBody['where'] = $queryParameters['where'];
            }
        } else {
            var_dump($query);
            echo("The class Client reporeted internal function getBody out of focus - [FATAL]\n Client->getBody exiting.\n");
            exit();
        }

        if(count($postRequestBody) === 0){

            $postRequestBody = new stdClass();
        }

        return $postRequestBody;
    }

    private function getUrl($query)
    {
        if($query === null) {

            return '/';
        }

        $queryString = null;
        $queryParameters = $query->getParameters();
        $queryBaseUrl = $query->getBaseUrl();
        $getParameters = array();

        if($queryBaseUrl === '/drop_table')
        {
            if($queryParameters['table_name']) {

                $getParameters['table_name'] = $queryParameters['table_name'];

            } elseif ( !$queryParameters['table_name'] ) {

                $getParameters['table_name'] = null;

            }
        } elseif($queryBaseUrl === '/get_table_info') {

            if($queryParameters['table_name']) {

                $getParameters['table_name'] = $queryParameters['table_name'];

            }elseif(!$queryParameters['table_name']) {

                $getParameters['table_name'] = null;
            }
        } elseif($queryBaseUrl === '/get_table_list') {

            $queryBaseUrl = $this->_url.$queryBaseUrl;
            //echo "get table list found: ".$queryBaseUrl."\n";
            return $queryBaseUrl;
        }

        $queryString = $this->httpAssocEncode($getParameters);
        echo "QUERY: ".$queryString."\n";

        if($queryString !== null)
        {
            $queryBaseUrl = $this->_url.$queryBaseUrl.$queryString;        
        }

        return $queryBaseUrl;
    }

    public function query($query)
    {
        $results = null;
        $queryMethodType = $query->getMethodType();

        if(!$query) {

            throw new Exception("Query was null in client->query(), Query must have content.");
            exit();
        }

        if ($queryMethodType === 'del') {

            $results = $this->clientDelete($query);

        } elseif ($queryMethodType === 'get') {

            $results = $this->clientGet($query);

        } elseif ($queryMethodType === 'post') {

            $results = $this->clientPost($query);

        } else {

            throw new Exception("client->query() reported being out of range - [FATAL]");
            exit();
        }

        return $results;
    }

    private function clientPost($query)
    {
        $key = $this->_key;
        $secret = $this->_secret;
        $postRequestBody = $this->getBody($query);
        $httpAuthString = (string) $key.":".$secret;
        $requestUrl = $this->getUrl($query);
        $curlHandle = $this->curlCreator();
        $completeInfoArray = $query->getParameters();
        $results = null;

        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_USERPWD, $httpAuthString);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($postRequestBody));
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);

        $results = $this->handleResults($curlHandle);

        return $results;
    }

    private function clientGet($query)
    {
        $key = $this->_key;
        $secret = $this->_secret;
        $httpAuthString = (string) $key.":".$secret;
        $requestUrl = $this->getUrl($query);
        $curlHandle = $this->curlCreator();
        $completeInfoArray = $query->getParameters();
        $postRequestBody = null;
        $results = null;

        curl_setopt($curlHandle, CURLOPT_USERPWD, $httpAuthString);
        curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);

        $results = $this->handleResults($curlHandle);

        return $results;
    }

    private function clientDelete($query)
    {
        $key = $this->_key;
        $secret = $this->_secret;
        $httpAuthString = (string) $key.":".$secret;
        $requestUrl = $this->getUrl($query);
        $curlHandle = $this->curlCreator();
        $completeInfoArray = $query->getParameters();
        $postRequestBody = null;
        $results = null;

        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curlHandle, CURLOPT_USERPWD, $httpAuthString);
        curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);

        $results = $this->handleResults($curlHandle);

        return $results;
    }

    private function handleResults($curlHandle)
    {
        //handle results gets curl handle, executes it, and
        //then returns pertinant results about the interaction
        $curlExecResult = null; //variable for curl execution handle
        $curlInfo = null;
        $responseObject = null;

        $curlExecResult = curl_exec($curlHandle); // now actually making the only outside call  
        $curlInfo = curl_getinfo($curlHandle);
        $this->close($curlHandle);

        $responseObject = $this->getDebugInfo($curlInfo, $curlExecResult);

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

    public function setKey($key) {

        $this->_key = $key;

        if( (isset($this->_key)) 
            && ($this->_key === $key)
        ) {

            return true;

        } else {

            return false;
        }
    }

    public function setSecret($secret) {

        $this->_secret = $secret;

        if( (isset($this->_secret)) 
            && ($this->_secret === $secret)
        ) {

            return true;

        } else {

            return false;
        }
    }

    private function isNotNull($mixedVar)
    {
        return !is_null($mixedVar);
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function getSecret()
    {
        return $this->_secret;
    }

    public function getClientUrl()
    {
        return $this->_url;
    }

    public function close($curlHandle)
    {
        try {
            curl_close($curlHandle);
        } catch(Exception $e) {
            echo $e."\n";
            return(false);
        }

        return(true);
    }

    private function getDebugInfo($curlInfo, $curlExecResult)
    {
        $curlExecResultArray = $this->resultsMediator($curlExecResult, $curlInfo);
        
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
}

?>