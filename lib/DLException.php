<?php

class DLException extends Exception {

    private $url;

    public function __construct($statusCode) {
        $this->code = $statusCode;
        //$this->message = $response;
        //$this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }
}

?>
