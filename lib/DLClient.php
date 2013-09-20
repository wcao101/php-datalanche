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
            $this->_verifySsl = false;
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

        if($secret != null) {
            $this->_secret = $secret; 
        }
        if ($key != null) {
            $this->_key = $key;
        }

        return $this;
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

    public function getKey()
    {
        return $this->_key;
    }

    public function getSecret()
    {
        return $this->_secret;
    }

    public function setKey($key) 
    {

        $this->_key = $key;

        if( (isset($this->_key)) 
            && ($this->_key === $key)
        ) {

            return true;

        } else {

            return false;
        }
    }

    public function setSecret($secret) 
    {

        $this->_secret = $secret;

        if( (isset($this->_secret)) 
            && ($this->_secret === $secret)
        ) {

            return true;

        } else {

            return false;
        }
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

    private function curlCreator($httpAuthString, $postRequestBody, $requestUrl)
    {
        /*
        * WARNING: changing a value in this array will change all related values across request types
        */
        $options = array (
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_URL => $requestUrl,
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_POST => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $httpAuthString,
            CURLOPT_SSLVERSION => 3,
            CURLOPT_SSL_VERIFYPEER => $this->_verifySsl,
            CURLOPT_SSL_VERIFYHOST => $this->_verifySsl,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_USERAGENT => 'Datalanche PHP Client',
            CURLOPT_VERBOSE => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_POSTFIELDS => json_encode($postRequestBody)
            );

        /**
        * @var resource/curl contains the curl handle
        */
        $curlHandle = curl_init();
        
        //MUST USE setopt_array if array!
        curl_setopt_array($curlHandle, $options);

        return $curlHandle;
    }

    private function clientPost($query)
    {
        $key = $this->_key;
        $secret = $this->_secret;
        $postRequestBody = $this->getBody($query);
        $httpAuthString = (string) $key.":".$secret;
        $requestUrl = $this->getUrl($query);
        $curlHandle = $this->curlCreator($httpAuthString, $postRequestBody, $requestUrl);

        $results = $this->handleResults($curlHandle);

        return $results;
    }

    private function getBody($query)
    {
        
        $queryParameters = $query->getParameters();
        $queryBaseUrl = $query->getUrl();
        $postRequestBody = array();

        if(($query === null)) {
            throw new Exception("The query for function getBody() was null\n");
            exit();

        } else {
            if( (count($queryParameters) === 0))
            {
                return new stdClass();
            }
            return $queryParameters;
        }
    }

    private function getDebugInfo($curlInfo, $curlExecResult)
    {
        $curlExecResultArray = $this->parseCurlResult($curlExecResult, $curlInfo);
        
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

    private function getUrl($query)
    {
        if($query === null) {

            return '/';
        }

        $queryBaseUrl = $query->getUrl();

 
        $queryBaseUrl = $this->_url.$queryBaseUrl;     

        return $queryBaseUrl;
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

    /**
    * parseCurlResult was built off the need to functionalize the parsing methods
    * required to synthesize the headers and response body from the semi unhelpful datatype
    * that the curl libraries return by default (a giant string).
    * parseCurlResult explodes the results and requests into keyed arrays contained by
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

    private function parseCurlResult($serverResponseString, $curlInfoArray)
    {
        // Unecessary but helpful to demonstrate the basic structure of the return object
        $statusArray = array();
        $statusArray['request'] = array();
        $statusArray['response'] = array();
        $statusArray['request']['header'] = array();
        $statusArray['response']['header'] = array();
        $statusArray['response']['body'] = array();

        //Seperate the header from the body in the return string and store both
        $responseHeader = substr($serverResponseString, 0, $curlInfoArray['header_size']);
        $responseBody = substr($serverResponseString, $curlInfoArray['header_size']);
        $responseHeader = explode("\n", $responseHeader);
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

    public function query($query)
    {
        $results = null;

        if ( (!$query)
            || ($query === null) 
        ) {
            throw new Exception("Query was null in client->query(), Query must have content.");
            exit();
        } else {

             $results = $this->clientPost($query);
        }

        return $results;
    }



}

?>