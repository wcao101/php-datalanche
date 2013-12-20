<?php

class DLException extends Exception {

    private $_errorType;
    private $_request;
    private $_response;

    public function __construct($result) {

        $this->_errorType = $result['data']['code'];
        $this->_request = $result['request'];
        $this->_response = $result['response'];
        $this->_response['body'] = $result['data'];

        $code = $result['response']['http_status'];
        $message = $result['data']['message'];
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . " [{$this->_errorType}]: {$this->message}\n";
    }

    public function getErrorType()
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
