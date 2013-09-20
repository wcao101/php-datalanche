<?php

class DLException extends Exception {

    private $_errorType;
    private $_request;
    private $_response;

    public function __construct($debugObject) {
        $this->_code = $debugObject['response']['http_status'];
        $this->_errorType = $debugObject['body']['name'];
        $this->_message = $debugObject['body']['message'];
        $this->_request = $debugObject['request'];
        $this->_response = $debugObject['response'];
        $this->_response['body'] = $debugObject['body'];
    }

    public function getErrorMessage() {
        return $this->_errorType;
    }

    public function getRequest() {
        return $this->_request;
    }

    public function getResponse() {
        return $this->_response;
    }

}

?>
