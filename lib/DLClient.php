<?php

include 'DLQuery.php';
include 'DLException.php';

class DLClient 
{
    private $_authKey;
    private $_authSecret;
    private $_url;
    private $_verifySsl;

    public function __construct($key, $secret, $host, $port, $verifySsl)
    {
        $this->_authKey = '';
        $this->_authSecret = '';
        $this->_url = 'https://api.datalanche.com';
        $this->_verifySsl = $verifySsl;

        if ($host != NULL) {
            $this->_url = 'https://' . host;
        }

        if ($port != NULL) {
            $this->_url .= ':' . $port;
        }

        if ($key != NULL) {
            $this->_authKey = $key;
        }

        if ($secret != NULL) {
            $this->_authSecret = $secret; 
        }

        return $this;
    }

    public function key($key) 
    {
        $this->_authKey = $key;
    }

    public function secret($secret) 
    {
        $this->_secret = $secret;
    }

    public function query($query)
    {
        if ($query === NULL) {
            throw new Exception('$query = NULL');
        }

        $url = $this->_url;

        $params = $query->getParams();
        if (array_key_exists('database', $params) == true) {
            $url .= '/' . urlencode((string)$params['database']);
            unset($params['database']);
        }

        $url .= '/query';
        $body = $params;
        $connection = curl_init();

        $options = array(
            CURLINFO_HEADER_OUT => true,
            CURLOPT_CONNECTTIMEOUT => 10, // seconds
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSLVERSION => 3,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => $this->_verifySsl,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Datalanche PHP Client',
            CURLOPT_USERPWD => $this->_authKey . ':' . $this->_authSecret,
            CURLOPT_VERBOSE => false
        );
        curl_setopt($connection, $options);

        $curlResult = curl_exec($connection);
        $curlError = curl_error($connection);
        $curlInfo = curl_getinfo($connection);
        curl_close($connection);

        if ($curlResult === false) {
            throw new Exception('cURL error: ' . $curlError);
        }

        $result = $this->parseResult($curlInfo, $curlResult);
        $httpStatus = $result['response']['http_status'];

        if ($httpStatus < 200 || $httpStatus > 300) {
            throw new DLException($result);
        }

        return $result;
    }

    private function parseResult($curlInfo, $curlResult)
    {
        $curlResult = $this->parseCurlResult($curlInfo, $curlResult);
        
        $result = array(
            'request' => array(
                'method' => $curlResult['request']['header']['operation'],
                'url' => $curlResult['curl_info_array']['url'],
                'headers' => $curlResult['request']['header'],
                'body' => $curlResult['request']['header']['url_parameters']
            ),
            'response' => array(
                'http_status' => $curlResult['response']['header']['http_code'],
                'http_version' => $curlResult['request']['header']['http_version'],
                'headers' => $curlResult['response']['header']
            ),
            'data' => $curlResult['response']['body']
        );

        return $result;
    }

    //
    // parseCurlResult was built off the need to functionalize the parsing methods
    // required to synthesize the headers and response body from the semi unhelpful datatype
    // that the curl libraries return by default (a giant string).
    // parseCurlResult explodes the results and requests into keyed arrays contained by
    // a singular array which the user can then refrence to get request/response headers,
    // general info, and response body content. This function also contains the json error
    // check which is used to throw specific errors at the json_decode level.
    //
    // @access private
    // @param string $curlResult the results of $this->handleResults(curl_exec($curlHandle)) [the response of the server to the request]
    // @param array $curlInfo the results of $this->handleResults(curl_get_info($curlHandle)) POST execution of the handle
    // @uses json_decode() the only place in client where this function is called and checked for errors
    // @uses json_last_error() part of a switch which throws specific json errors
    // @return array $statusArray is an array of arrays contating info about the curl response & request
    //
    private function parseCurlResult($curlInfo, $curlResult)
    {
        $statusArray = array();
        $statusArray['request'] = array();
        $statusArray['response'] = array();
        $statusArray['request']['header'] = array();
        $statusArray['response']['header'] = array();
        $statusArray['response']['body'] = array();

        // Seperate the header from the body in the return string and store both
        $responseHeader = substr($curlResult, 0, $curlInfo['header_size']);
        $responseBody = substr($curlResult, $curlInfo['header_size']);
        $responseHeader = explode("\n", $responseHeader);
        $statusArray['response']['header']['status'] = $responseHeader[0];

        array_shift($responseHeader);

        foreach ($responseHeader as $value) {
            $middle = explode(":", $value);
            if (count($middle) <= 1) {
                // The explode function has returned an empty row result
                // which means that the current slot is porbably part of
                // the whitespace returned in the response. This happens when
                // the curl library appends the header is appended to the response string.
                // Therefore skip appending it to the content array
                // and move to the next value slot.
            } else {
                $statusArray['response']['header'][trim($middle[0])] = trim($middle[1]);
            }
        }

        // Decode the response body and recursivley set any objects to assosiative arrays.
        $responseBody = json_decode($responseBody, true);

        // Check for errors related to json decoding,
        // this helps to isolate specific errors with
        // json responses from the server.
        switch (json_last_error()) {
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
        $statusArray['response']['header']['http_code'] = $curlInfo['http_code'];

        // We move to do the same process again but this time on the request headers
        // we won't have to parse for a json body this time either, so the 
        // process is a bit shorter.

        $requestHeader = explode("\n", $curlInfo['request_header']);
        $statusArray['request']['header']['operation'] = explode(" ", $requestHeader[0]);
        $statusArray['request']['header']['url_parameters'] = $statusArray['request']['header']['operation'][1];
        $statusArray['request']['header']['http_version'] = $statusArray['request']['header']['operation'][2];
        $statusArray['request']['header']['operation'] = $statusArray['request']['header']['operation'][0];

        array_shift($requestHeader);

        foreach($requestHeader as $value) {

            $middle = explode(":", $value);
            if (count($middle) <= 1) {
                // the explode function has created a white-space entry
            } else {
                $statusArray['request']['header'][trim($middle[0])] = trim($middle[1]);
            }
        }

        return $statusArray;
    }
}

?>
