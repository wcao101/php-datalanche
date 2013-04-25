<?php

require_once('read-params.php');

class DLClient {

    private $authKey;
    private $authSecret;
    private $url;
    private $verifySsl;

    public function __construct($key = '', $secret = '', $host = NULL, $port = NULL, $verifySsl = True) {
        $this->authKey = $key;
        $this->authSecret = $secret;
        $this->url = 'https://api.datalanche.com';
        $this->verifySsl = $verifySsl;

        if ($host !== NULL) {
            $this->url = 'https://' . $host;
        }
        if ($port !== NULL) {
            $this->url .= ':' . $port;
        }
    }

    public function getList() {
        $url = $this->url . '/list';
        return $this->getRequest($url);
    }

    public function getSchema($datasetName) {
        $url = $this->url . '/schema';
        $url .= '?dataset=' . urlencode($datasetName);
        return $this->getRequest($url);
    }

    public function read($params = NULL) {
        if ($params !== NULL && !($params instanceof DLReadParams)) {
            throw new Exception('$params not instanceof DLReadParams');
        }

        $url = $this->url . '/read';

        if ($params !== NULL && $params->dataset !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'dataset=' . urlencode($params->dataset);
        }
        if ($params !== NULL && $params->fields !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'fields=' . urlencode($this->array2str($params->fields));
        }
        if ($params !== NULL && $params->limit !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'limit=' . urlencode($params->limit);
        }
        if ($params !== NULL && $params->skip !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'skip=' . urlencode($params->skip);
        }
        if ($params !== NULL && $params->sort !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'sort=' . urlencode($this->array2str($params->sort));
        }
        if ($params !== NULL && $params->total !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'total=' . urlencode($this->bool2str($params->total));
        }

        return $this->getRequest($url);
    }

    private function addUrlSeparator($url) {

        if (strpos($url, '?') !== FALSE) {
            $url .= '&';
        } else {
            $url .= '?';
        }

        return $url;
    }

    // convert array to comma-separated string
    private function array2str($value) {
        $type = gettype($value);
        if ($type === 'array') {
            $value = implode(',', $value);
        }
        return $value;
    }

    // fix toString(boolean) when false
    private function bool2str($value) {
        if ($value === false) {
            return 0;
        }
        return $value;
    }

    private function getRequest($url) {

        $connection = curl_init();

        curl_setopt($connection, CURLOPT_HEADER, false);
        curl_setopt($connection, CURLOPT_ENCODING, 'gzip');
        curl_setopt($connection, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($connection, CURLOPT_USERPWD, $this->authKey . ':' . $this->authSecret);
        curl_setopt($connection, CURLOPT_SSLVERSION, 3);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($connection, CURLOPT_FORBID_REUSE, false);
        curl_setopt($connection, CURLOPT_USERAGENT, 'Datalanche PHP client');
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 10); // seconds
        curl_setopt($connection, CURLOPT_URL, $url);

        $data = curl_exec($connection);
        $httpStatus = curl_getinfo($connection, CURLINFO_HTTP_CODE);

        curl_close($connection);

        if ($data === false) {
            throw new Exception('cURL error: ' . curl_error($connection));
        }

        $json = json_decode($data, true);

        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new Exception('JSON error: Maximum stack depth exceeded');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception('JSON error: Underflow or the modes mismatch');
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception('JSON error: Unexpected control character found');
                break;
            case JSON_ERROR_SYNTAX:
                throw new Exception('JSON error: Syntax error, malformed JSON');
                break;
            case JSON_ERROR_UTF8:
                throw new Exception('JSON error: Malformed UTF-8 characters, possibly incorrectly encoded');
                break;
            case JSON_ERROR_NONE:
            default:
                break;                 
        }

        if ($httpStatus < 200 || $httpStatus > 300) {
            throw new Exception($data);
        }

        return $json;
    }
}
?>
