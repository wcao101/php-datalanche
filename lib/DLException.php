<?php

class DLException extends Exception {

    private $_errorType;
    private $_request;
    private $_response;

    public function __construct($result)
    {
        $this->_code = $result['response']['http_status'];
        $this->_errorType = $result['data']['code'];
        $this->_message = $result['data']['message'];
        $this->_request = $result['request'];
        $this->_response = $result['response'];
        $this->_response['body'] = $result['data'];
    }

    public function getErrorMessage()
    {
        return $this->_errorType;
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function getResponse()
    {
        return $this->_response;
    }
}

?>
