<?php

class DLException extends Exception {

    private $url;

    public function __construct($statusCode, $response, $url) {
        $this->code = $statusCode;
        $this->message = $response;
        $this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }
}

?>
