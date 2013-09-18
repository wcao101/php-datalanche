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


/**
* \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
* Class DLClient contains the overwhelming amount of functionality
* regarding connection creation, control and closure.
* DLC client does post and get parsing for provided input.
* Post bodies are formatted using json_encode functionality while
* get parameters are serialzed into a url string using basic
* associative encoding procedure. Upon instantiation of a 
* new DLClient automatic execution procedure (__construct) 
* expects several set-up inputs for succesful client configuration. 
* Specifically: your api sercret, api key, desired host, 
* port, and whether or not you would like to strictly use secure (https)
* connections for interacting with the specified host. 
* Disabling '$_verifySsl' (that is setting it to false or null)
* is ideal for development procedures or self-signed certs.
*
* @category     raw PHP/Datalanche
* @package      datalanche
* @subpackage   client
* @copyright    Copyright (c) 2013 Datalanche (https://www.datalanche.com/)
* @license      https://www.datalanche.com/terms
* @version      Release: @package_version@
* @link         https://github.com/datalanche/php-datalanche.git
* @since        @release_version@
* @deprecated   still in use
* //////////////////////////////////////////////////
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

    /**
    * Inital execution construct for client class.
    * Configures and parses basic varaible submission
    * for the set-up of client <-> database interaction.
    *
    * @access public
    * @param string $secret
    * @param string $key
    * @param string $host
    * @param string|int $port
    * @param bool|string $ssl
    * @return mixed/object return $this or this object upon completion
    */
    public function __construct ($secret, $key, $host, $port, $ssl)
    {
        /*BASIC ASSIGNMENTS*/
        $url = 'https://';  //Preface the url string with the intended protocol.

        /* BEGIN BASIC LOGIC TO DETERMINE ARGUMEENTS*/

        //Testing for whether a custom host was provided
        if(($host === null)
            || ($host === '')
        ) {
            //If there isn't a custom host, revert to datalanche
            $this->_host = 'api.datalanche.com'; 
        } else {
            //Otherwise we are using a custom host.
            $this->_host = $host; 
        }

        $url = $url.$host; //Now we'll take the url string and append the host to the end of it.

        //Testing to see whether the use has provided a custom port
        if (($port === null)
            || ($port === '')
        ) {
            //If there is no provided port, default to settings used by Datalanche
            $this->_port = null; 
        } else {
            //Otherwise get the port and make the needed modification to the url string
            $this->_port = $port; 
            $url = $url.':'.$port;
        }

        //Now we have the full base url, so we can go ahead and assign it to the object.
        $this->_url = $url;

        //Checking to make sure to verify ssl certs.
        if (($ssl === false)
            || ($ssl === 0)
            || ($ssl === 'false')
        ) {
            $this->_verifySsl = false; //Verify ssl is off, unverfied certification possible.
        } elseif(($ssl === null)
            || ($ssl === true) 
            || ($ssl === 1) 
            || ($ssl === 'true')
        ) {
            /* If verify ssl is null (default value if undeclared) then we default
            to using secure and verified communications; we do the same if explicitly
            qualified as true as well. */
            $this->_verifySsl = true;
        }

        /*Now testing for provided api key & secret.
        If these value are null then they are left unset (null).*/
        if($secret != null) {
            $this->_secret = $secret; 
        }
        if ($key != null) {
            $this->_key = $key;
        }

        /*
        *RETURN THE OBJECT AFTER EXECUTION
        */
        return $this;
    }

    /**
    * httpAssocEncopde is built as a stripped down alternative to the built
    * in functionality of http_build_query which does not set null entries
    * during url encoding, and therefore is not compliant with datalanche
    * testing/operating SOP. The function expects a one dimensional (depth
    * of one) array of keyed values. They key is then encoded as the variable
    * name and the accompaying array value slot is set as the value of the (new)
    * get param (encoded simply as a string for url use). This function is
    * unoptimized and not tested for signifigant load.
    *
    * @access private
    * @param array $array a single dimension (depth of one) array which contains a list of keyed values
    * @return string $requestOption a string of compiled get arguments
    */
    private function httpAssocEncode($array)
    {
        $requestOption = ''; //this is the intended holder of the final string, set it to nothing first.
        $end = end($array); //get the end of the array

        /*
        * I disclaim the following functionality as a temporary, hacky way 
        * to finsih the intended task, simple and stripped down as it is.
        */

        foreach($array as $key => $entry) { 
            /*
            * Iterate through the entries and check to see if they are null.
            * The following check and assignment is the only reason this code
            * exists as http_build_query regards keyed values in an array set
            * to null as non-existent, therefore not allowing unset variables.
            */

            /*
            * The following check & assigment is redundant. PHP, though, might be better off 
            * setting $entry = '' Running perscribed tests with either value does not 
            * produce diffrences in results or execution since either values looks the
            * same on the get param line e.g. localhost:4001?var_one=&var_two=value
            * where var_one is the entry that was set to null when passed to client.
            */
            if($entry === null) {
                $entry = null;
            }
            
            /*
            * Check to see if we are at the end of the array
            * if we are then we won't append the '&' because
            * we are at the end of the params list.
            */
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

    /**
    * curlCreator is a functionalization of basic curl
    * params which are consistent across request types -
    * (get/post/[get]delete). From the programs last major version iteration
    * curl construction templates have been changed to include header request
    * and response info, necessitating further functionality to parse how
    * curl decides to store and read this information.
    *
    * @access private
    * @return mixed/resource $curlHandle this is the current curl handle, used to formulate and execute requests
    * @uses curl_init() a curl resource to being a curl connection
    * @uses curl_setopt_array() a curl resource which allows the setting of parameters through an array
    */

    private function curlCreator()
    {
        /*
        * The options array 
        * WARNING: changing a value in this array will change all related values across request types
        */
        $options = array (
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_SSLVERSION => 3,
            CURLOPT_SSL_VERIFYPEER => $this->_verifySsl,
            CURLOPT_SSL_VERIFYHOST => $this->_verifySsl,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_USERAGENT => 'Datalanche PHP Client',
            CURLOPT_VERBOSE => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLINFO_HEADER_OUT => true
            );

        /**
        * @var resource/curl contains the curl handle
        */
        $curlHandle = curl_init(); //We are initalizing the curl connection here
        
        //Give it the values we just laid out
        curl_setopt_array($curlHandle, $options);

        return $curlHandle;
    }

    /**
    * resultsMediator was built off the need to functionalize the parsing methods
    * required to synthesize the headers and response body from the semi unhelpful datatype
    * that the curl libraries return by default (a giant string).
    * resultsMediator explodes the results and requests into keyed arrays contained by
    * a singular array which the user can then refrence to get request/response headers,
    * general info, and response body content. This function also contains the json error
    * check which is used to throw specific errors at the json_decode level.
    *
    * @access private
    * @param string $serverResponseString the results of $this->handleResults(curl_exec($curlHandle)) [the response of the server to the request]
    * @param array $curlInfoArray the results of $this->handleResults(curl_get_info($curlHandle)) POST execution of the handle
    * @uses json_decode() the only place in client where this function is called and checked for errors
    * @uses json_last_error() part of a switch which throws specific json errors
    * @return array $statusArray is an array of arrays contating info about the curl response & request
    */

    private function resultsMediator($serverResponseString, $curlInfoArray)
    {
        // Unecessary but helpful to demonstrate the basic structure of the return object
        $statusArray = array();
        $statusArray['request'] = array();
        $statusArray['response'] = array();
        $statusArray['request']['header'] = array();
        $statusArray['response']['header'] = array();
        $statusArray['response']['body'] = array();

        //Get the header minus the possible body
        $responseHeader = substr($serverResponseString, 0, $curlInfoArray['header_size']);
        //whatever is left over is the response body
        $responseBody = substr($serverResponseString, $curlInfoArray['header_size']);
        //Explode the header across the newlines, which is the way each header value slot is seperated
        $responseHeader = explode("\n", $responseHeader);
        //Header status will always be the 0th entry
        $statusArray['response']['header']['status'] = $responseHeader[0];

        array_shift($responseHeader);

        /*
        * Loop over the rest of the header and explode along the colon.
        * Take the content before the colon and assign it as a new array key
        * while taking the content after the colon and assigning it as that key's
        * value.
        *
        * This radically simplifies access to any given part of response or request.
        */
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

        /*
        * Decode the response body and recursivley set any objects to assosiative arrays.
        */ 
        $responseBody = json_decode($responseBody, true);

        /*
        * Check for errors related to json decoding,
        * this helps to isolate specific errors with
        * json responses from the server.
        */
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
                throw new Exception('JSON error: Malformed UTF-8 charachters, possibly incorrectly encoded');
                break;
            case JSON_ERROR_NONE:
            default:
                break;
        }

        $statusArray['response']['body'] = $responseBody;
        $statusArray['response']['header']['http_code'] = $curlInfoArray['http_code'];

        /*
        * We move to do the same process again but this time on the request headers
        * we won't have to parse for a json body this time either, so the 
        * process is a bit shorter.
        */

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

        /* 
        * Appending the entierty of the curl_get_info
        * in its untouched, unprocessed version since some
        * of the info in there might be helpful to debugging
        * and development
        */ 

        $statusArray['curl_info_array'] = $curlInfoArray;

        return $statusArray;
    }

    /**
    * BOOKMARK
    */

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