<?php

class DLClient {

    private $authKey;
    private $authSecret;
    private $url;
    private $verifySsl;

    public function __construct($key = '', $secret = '', $host = NULL, $port = NULL, $verifySsl = True) {
        $this->authKey = $key;
        $this->authSecret = $secret;
        $this->host = 'api.datalanche.com';
        $this->verifySsl = $verifySsl;

        if ($host !== NULL) {
            $this->url = 'https://' . $host;
        }
        if ($port !== NULL) {
            $this->url .= ':' . $port;
        }
        if ($verifySsl != NULL){
            $this->verifySsl = $verifySsl
        }
    }

    public function setApiKey($key) {
        $this->authKey = $key;
    }

    public function setApiSecret($secret) {
        $this->authSecret = $secret;
    }

    public function addColumns($datasetName, $columns) {
        $url = $this->url . '/add_columns';

        if ($datasetName !== NULL) {
            $url .= '?dataset=' . urlencode($datasetName);
        }

        $body = array(
            'num_columns' => count($columns),
            'columns' => $columns
        );

        $this->postRequest($url, $body);
    }

    public function createDataset($schema) {
        $url = $this->url . '/create_dataset';

        $this->postRequest($url, $schema);
    }

    public function deleteDataset($datasetName) {
        $url = $this->url . '/delete_dataset';

        if ($datasetName !== NULL) {
            $url .= '?dataset=' . urlencode($datasetName);
        }

        $this->deleteRequest($url);
    }

    public function deleteRecords($datasetName, $filter = NULL) {
        $url = $this->url . '/delete_records';

        if ($datasetName !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'dataset=' . urlencode($datasetName);
        }
        if ($filter !== NULL) {
            $url = $this->addUrlSeparator($url);
            if ($filter instanceof DLFilter) {
                $filter = $filter->toString();
            }
            $url .= 'filter=' . urlencode($this->bool2str($filter));
        }

        $this->deleteRequest($url);
    }

    public function getDatasetList() {
        $url = $this->url . '/get_dataset_list';
        return $this->getRequest($url);
    }

    public function getSchema($datasetName) {
        $url = $this->url . '/get_schema';

        if ($datasetName !== NULL) {
            $url .= '?dataset=' . urlencode($datasetName);
        }

        return $this->getRequest($url);
    }

    public function insertRecords($datasetName, $records) {
        $url = $this->url . '/insert_records';

        if ($datasetName !== NULL) {
            $url .= '?dataset=' . urlencode($datasetName);
        }

        $body = array(
            'num_records' => count($records),
            'records' => $records
        );

        $this->postRequest($url, $body);
    }

    public function readRecords($datasetName, $params = NULL) {
        if ($params !== NULL && !($params instanceof DLReadParams)) {
            throw new Exception('$params not instanceof DLReadParams');
        }

        $url = $this->url . '/read_records';

        // PHP is stupid when it converts boolean to string.
        // That is why each parameter is wrapped with bool2str().
        // array2str() handles boolean values itself.

        if ($datasetName !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'dataset=' . urlencode($datasetName);
        }
        if ($params !== NULL && $params->columns !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'columns=' . urlencode($this->array2str($params->columns));
        }
        if ($params !== NULL && $params->filter !== NULL) {
            $url = $this->addUrlSeparator($url);
            $filter = $params->filter;
            if ($filter instanceof DLFilter) {
                $filter = $filter->toString();
            }
            $url .= 'filter=' . urlencode($this->bool2str($filter));
        }
        if ($params !== NULL && $params->limit !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'limit=' . urlencode($this->bool2str($params->limit));
        }
        if ($params !== NULL && $params->skip !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'skip=' . urlencode($this->bool2str($params->skip));
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

    public function removeColumns($datasetName, $columns) {
        $url = $this->url . '/remove_columns';

        if ($datasetName !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'dataset=' . urlencode($datasetName);
        }
        if ($columns !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'columns=' . urlencode($this->array2str($columns));
        }

        $this->deleteRequest($url);
    }

    public function setDetails($datasetName, $details) {
        $url = $this->url . '/set_details';

        if ($datasetName !== NULL) {
            $url .= '?dataset=' . urlencode($datasetName);
        }

        $this->postRequest($url, $details);
    }

    public function updateColumns($datasetName, $columns) {
        $url = $this->url . '/update_columns';

        if ($datasetName !== NULL) {
            $url .= '?dataset=' . urlencode($datasetName);
        }

        $this->postRequest($url, $columns);
    }

    public function updateRecords($datasetName, $records, $filter = NULL) {
        $url = $this->url . '/update_records';

        if ($datasetName !== NULL) {
            $url = $this->addUrlSeparator($url);
            $url .= 'dataset=' . urlencode($datasetName);
        }
        if ($filter !== NULL) {
            $url = $this->addUrlSeparator($url);
            if ($filter instanceof DLFilter) {
                $filter = $filter->toString();
            }
            $url .= 'filter=' . urlencode($this->bool2str($filter));
        }

        $this->postRequest($url, $records);
    }

    private function addUrlSeparator($url) {

        if (strpos($url, '?') !== false) {
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
            $str = '';
            for ($i = 0; $i < count($value); $i++) {
                $item = $this->bool2str($value[$i]);
                $str = $str . $item;
                if ($i < count($value) - 1) {
                    $str = $str . ',';
                }
            }
            $value = $str;
        } else {
            $value = $this->bool2str($value);
        }
        return $value;
    }

    // fix toString(boolean) when false
    private function bool2str($value) {
        if ($value === false) {
            return 'false';
        } else if ($value === true){
            return 'true';
        }
        return $value;
    }

    private function deleteRequest($url) {

        $connection = curl_init();

        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, 'DELETE');
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
            throw new DLException($httpStatus, $data, $url);
        }
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
            throw new DLException($httpStatus, $data, $url);
        }

        return $json;
    }

    private function postRequest($url, $body) {

        $connection = curl_init();

        curl_setopt($connection, CURLOPT_POST, true);
        curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($connection, CURLOPT_HEADER, false);
        curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
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
            throw new DLException($httpStatus, $data, $url);
        }
    }
}
?>
