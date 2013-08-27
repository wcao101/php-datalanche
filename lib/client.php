<?php
/*
* client.php
* created by: cristian cavalli
* property of: Datalanche Inc.
* purpose: allow interaction between php clients and
* dalanche services
* target: datalanche
*/
/* DEPENDICES */
include 'query.php';
include 'expression.php';
//include 'DLException.php';

class Client 
{
    public $parameters;
    public $key;
    public $secret;
    public $url;
    public $verify_ssl;
    public $connection;
    public $get_url;
    public $curl_status_array;

    public function __construct ($_secret, $_key, $_host, $_port, $_ssl)
    {
        //assignments
        $_url = 'https://';
        $this->curl_status_array = array();

        if($_host === null || $_host === '')
        {
            $this->host = 'api.datalanche.com';
        }else{
            $this->host = $_host;
        }

        $_url = $_url.$_host;

        if ($_port === null || $_port === '')
        {
            $this->port = null;
        }else{
            $this->port = $_port;
            $_url = $_url.':'.$_port;
        }

        $this->url = $_url;

        if ($_ssl === false || $_ssl === 0 || $_ssl === 'false'){
            $this->verify_ssl = $_ssl;
        }else if($_ssl === null || $_ssl === true || $_ssl === 1 || $_ssl === 'true'){
            $this->verify_ssl = true;
        }

        if($_secret != null)
        {
            $this->secret = $_secret; 
        }
        if ($_key != null)
        {
            $this->key = $_key;
        }

        return($this);
    }

    public function curlCreator()
    {
        $options = array (
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_SSLVERSION => 3,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_USERAGENT => 'Datalanche PHP Client',
            CURLOPT_VERBOSE => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            );
        $curl_request = curl_init();
        curl_setopt_array($curl_request, $options);
        return($curl_request);
    }

    public function getBody($query)
    {
        $body = array(
            'add_columns' => null,
            'alter_columns' => null,
            'name' => null,
            'description' => null,
            'drop_columns' => null,
            'is_private' => null,
            'license' => null,
            'rename' => null,
            'sources' => null,
            'columns' => null,
            'where' => null,
            'values' => null,
            'distinct' => null,
            'from' => null,
            'group_by' => null,
            'limit' => null,
            'offset' => null,
            'order_by' => null,
            'select' => null,
            'total' => null,
            'set' => null,
            'debug' => null
            );

        if($query === null){
            throw new Exception("The query for getBody() was null\n");
            return($body);
        }

        if(array_key_exists('debug', $query->parameters))
        {
            if($query->parameters['debug'] != null){
                $body['debug'] = $query->parameters['debug'];
            }
        }

        if($query->base_url === 'alter_table')
        {
            if($query->parameters['add_columns'] != null)
            {
                $body['add_columns'] = $query->parameters['add_columns'];
            }
            if($query->parameters['alter_columns'] != null)
            {
                $body['alter_columns'] = $query->parameters['alter_columns'];
            }
            if($query->parameters['name'] != null)
            {
                $body['name'] = $query->parameters['name'];
            }
            if($query->parameters['description'] != null)
            {
                $body['description'] = $query->parameters['description'];
            }
            if($query->parameters['drop_columns'] != null)
            {
                $body['drop_columns'] = $query->parameters['drop_columns'];
            }
            if($query->parameters['is_private'] != null)
            {
                $body['is_private'] = $query->parameters['is_private'];
            }
            if($query->parameters['license'] != null)
            {
                $body['license'] = $query->parameters['license'];
            }
            if($query->parameters['rename'] != null)
            {
                $body['is_private'] = $query->parameters['is_private'];
            }
            if($query->parameters['sources'] != null)
            {
                $body['sources'] = $query->parameters['sources'];
            }
        }

        elseif($query->base_url === '/create_table')
        {
            if($query->parameters['columns'] != null)
            {
                $body['columns'] = $query->parameters['columns'];
            }
            if($query->parameters['name'] != null)
            {
                $body['name'] = $query->parameters['name'];
            }
            if($query->parameters['description'] != null)
            {
                $body['description'] = $query->parameters['description'];
            }
            if($query->parameters['is_private'] != null)
            {
                $body['is_private'] = $query->parameters['is_private'];
            }
            if($query->parameters['license'] != null)
            {
                $body['license'] = $query->parameters['license'];
            }
            if($query->parameters['sources'] != null)
            {
                $body['sources'] = $query->parameters['sources'];
            }
        }

        elseif ($query->base_url === '/delete_from')
        {
            if($query->parameters['name'] != null)
            {
                $body['name'] = $query->parameters['name'];
            }
            if($query->parameters['where'] != null)
            {
                $body['where'] = $query->parameters['where'];
            }
        }

        elseif ($query->base_url === '/insert_into')
        {
            if($query->parameters['name'] != null)
            {
                $body['name'] = $query->parameters['name'];
            }
            if($query->parameters['values'] != null)
            {
                $body['values'] = $query->parameters['values'];
            }
        }

        elseif ($query->base_url === '/select_from')
        {
            if($query->parameters['distinct'] != null)
            {
                $body['distinct'] = $query->parameters['distinct'];
            }
            if($query->parameters['from'] != null)
            {
                $body['from'] = $query->parameters['from'];
            }
            if($query->parameters['group_by'] != null)
            {
                $body['group_by'] = $query->parameters['group_by'];
            }
            if($query->parameters['limit'] != null)
            {
                $body['limit'] = $query->parameters['limit'];
            }
            if($query->parameters['offset'] != null)
            {
                $body['offset'] = $query->parameters['offset'];
            }
            if($query->parameters['order_by'] != null)
            {
                $body['order_by'] = $query->parameters['order_by'];
            }
            if($query->parameters['select'] != null)
            {
                $body['select'] = $query->parameters['select'];
            }
            if($query->parameters['total'] != null)
            {
                $body['total'] = $query->parameters['total'];
            }
            if($query->parameters['where'] != null)
            {
                $body['where'] = $query->parameters['where'];
            }
        }

        elseif($query->base_url === '/update')
        {
            if($query->parameters['name'] != null)
            {
                $body['name'] = $query->parameters['name'];
            }
            if($query->parameters['set'] != null)
            {
                $body['set'] = $query->parameters['set'];
            }
            if($query->parameters['where'] != null)
            {
                $body['where'] = $query->parameters['where'];
            }
        }
        else
        {
            var_dump($query);
            throw new Exception("Out of range on get_body...\n exiting\n");
            exit();
        }

        return($body);
    }

    public function getUrl($query)
    {
        if($query === null)
        {
            return('/');
        }

        $url = $query->base_url;
        $parameters = array(
            'debug' => null,
            'name' => null
            );

        if($url === '/drop_table')
        {
            if($query->parameters['name'])
            {
                $parameters['name'] = $query->parameters['name'];
            }elseif(!$query->parameters['name']){
                $parameters['name'] = null;
            }
        }

        elseif($url === '/get_table_info')
        {
            if($query->parameters['name'])
            {
                $parameters['name'] = $query->parameters['name'];
            }elseif(!$query->parameters['name']){
                $parameters['name'] = null;
            }
        }

        elseif($url === '/get_table_list')
        {
            $url = $this->url.$url;
            echo "get table list found: ".$url."\n";
            return($url);
        }

        $_str = http_build_query($parameters);

        if($_str !== null)
        {
            $url = $this->url.$url.'?'.$_str;        
        }

        return($url);
    }

    public function query($query)
    {
        $_results = null;


        if(!$query)
        {
            throw new Exception("Query was null in client->query()\n");
            exit();
        }

        if($query->url_type === 'del')
        {
            $_results = $this->clientDelete($query);
            return($_results);
        }
        if($query->url_type === 'get')
        {
            $_results = $this->clientGet($query);
            return($_results);
        }
        if($query->url_type === 'post')
        {
            $_results = $this->clientPost($query);
            return($_results);
        }
    }

    public function clientPost($query)
    {
        $_key = null;
        $_secret = null;
        $_results = null;
        $_body = null;
        $http_auth_string = null;
        $request_url = null;
        $curl_request = null;

        if($query->test_key != null)
        {
            $_key = $query->test_key;
        }else {
            $_key = $this->key;
        }

        if($query->test_secret != null)
        {
            $_secret = $query->test_secret;
        }else {
            $_secret = $this->secret;
        }

        $_body = json_encode($this->getBody($query));
        $http_auth_string = (string) $_key.":".$_secret;
        $request_url = $this->getUrl($query);
        //$request_url = $this->url.$request_url."?".
            //http_build_query(
                //array('name'=>$query->parameters['name']));
        echo "POST URL FORMULATED: ".$request_url."\n";
        $curl_request = $this->curlCreator();
        curl_setopt($curl_request, CURLOPT_POST, true);
        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $_body);
        curl_setopt($curl_request, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl_request, CURLOPT_URL, $request_url);
        $_results = $this->handleResults($curl_request);

        return($_results);
    }

    public function clientGet($query)
    {
        $_key = null;
        $_secret = null;
        $_results = null;
        $http_auth_string = null;
        $request_url = null;
        $curl_request = null;

        ///test for subbed key
        if($query->test_key != null)
        {
            $_key = $query->test_key;
        }else {
            $_key = $this->key;
        }
        ///test for subbed secret
        if($query->test_secret != null)
        {
            $_secret = $query->test_secret;
        }else {
            $_secret = $this->secret;
        }

        $http_auth_string = (string) $_key.":".$_secret;
        $request_url = $this->getUrl($query);
        $curl_request = $this->curlCreator();
        curl_setopt($curl_request, CURLOPT_USERPWD, $http_auth_string);
        curl_setopt($curl_request, CURLOPT_URL, $request_url);
        $_results = $this->handleResults($curl_request);

        return($_results);
    }

    public function clientDelete($query)
    {
        $_key = null;
        $_secret = null;
        $_results = null;
        $http_auth_string = null;
        $request_url = null;
        $curl_request = null;

        if($query->test_key != null)
        {
             $_key = $query->test_key;
        }else {
             $_key = $this->key;
        }
        if($query->test_secret != null)
        {
            $_secret = $query->test_secret;
        }else {
            $_secret = $this->secret;
        }

        //now creating the specific authstring
        $http_auth_string = (string) $_key.":".$_secret;
        //setting url and curl_request handle
        $request_url = $this->getUrl($query);
        $curl_request = $this->curlCreator();

        curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl_request, CURLOPT_USERPWD, $http_auth_string);
        curl_setopt($curl_request, CURLOPT_URL, $request_url);
        $_results = $this->handleResults($curl_request);

        return($_results);
    }

    public function handleResults($curl_handle)
    {
        //handle results gets curl handle, executes it, and
        //then returns pertinant results about the interaction
        $curl_interaction = null; //variable for curl execution handle
        $last_curl_error = null; //variable for curl end session error output
        $curl_debug = null;
        $curl_info = null;
        $transaction_errors = null;
        $curl_debug = curl_getinfo($curl_handle, 1);
        try
        {
            $curl_interaction = curl_exec($curl_handle); // now actually making the only outside call
            //echo "///////////////////////////////\n";
            //echo "curl body:\n:";
            //var_dump($curl_interaction);
            //echo "..................\n";
            $last_curl_error = (string) curl_error($curl_handle);
            $curl_info = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
            $curl_debug = curl_getinfo($curl_handle, 1);
            $transaction_errors = array (
                'statusCode' => $curl_info,
                );
            if($transaction_errors['statusCode'] === null || $transaction_errors['statusCode'] === '')
            {

            }

                echo "++++++++++++++++++++++++++++++++++++++++++++\n";
                echo "GOT CODE: ".$transaction_errors['statusCode']."\n";
                echo "CURL INFO DUMP:\n";
                var_dump($curl_debug);
                echo "++++++++++++++++++++++++++++++++++++++++++++\n";
           // echo "LAST CURL ERROR: ".$curl_info."\n";
        } catch (Exception $e) {
            echo "curl error: ".$e;
        }

        if($last_curl_error){
            array_push($this->curl_status_array, json_decode($last_curl_error));
        }



        
        return($transaction_errors);
    }
}

?>