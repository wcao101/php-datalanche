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
include 'client_debug.php';

class client 
{
    private $auth_key;
    private $auth_secret;
    private $url;
    private $verify_ssl;
    private $connection;

    /*
    *THIS CONSTRUCT CREATES A BASIC OBJECT WITH ATTRIBUTES NECESSARY TO
    *INTERACT WITH PROPREITARY DATALANCHE DATA PROCESSING FUNCTIONALITY
    */
    public function __construct($key = NULL, $secret = NULL, $host = NULL, $port = NULL, $verify_ssl = True)
    {
        //Variable fallback overrides
        $this->host = 'api.datalanche.com';
        $this->port = NULL;
        $this->verify_ssl = True;
        //-end overrides

        //Simple assignments should go here
        $url = 'https://';

        $this->auth_key = NULL;
        $this->auth_secret = NULL;
        //-end assignments
        /////////////KEY INFO/////////////////
        if ($key != NULL)
        {
            $this->auth_key = $key;
        }
        if ($secret != NULL)
        {
            $this->auth_secret = $secret;
        }

        /////////////HOST INFO/////////////////
        if ($host != NULL)
        {
            $this->host = $host;
        }
        if ($port != NULL)
        {
            $this->port = $port;
        }
        //append and configure hostname url with https prefix and port

        $this->url = $url.$host;

            if($port != NULL)
            {
                $this->url = $this->url.':'.(string)$port;
            }
        $connection = curl_init();
        //ASSSIGN CURL INFO
            curl_setopt($connection, CURLOPT_HEADER, false);
            curl_setopt($connection, CURLOPT_ENCODING, 'gzip');
            curl_setopt($connection, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($connection, CURLOPT_USERPWD, $this->auth_key . ':' . $this->auth_secret);
            curl_setopt($connection, CURLOPT_SSLVERSION, 3);
            curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
            curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($connection, CURLOPT_FORBID_REUSE, false);
            curl_setopt($connection, CURLOPT_USERAGENT, 'Datalanche PHP client');
            curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 10); // seconds
            curl_setopt($connection, CURLOPT_URL, $this->url);
            $this->connection = $connection;
    }

    public function close()
    {
        curl_close($this->connection);
    }


    public function object_printer()
    {
        foreach ($this as $key => $value) 
        {
            print $key." : ".$value."\n";
        }
    }

    public function query($q)
    {
        if (!$q)
        {
            throw new Exception('The query was null.\n');
            $this->close();
        }
        elseif ($q->urlType === 'del')
        {
            $this->delete_request();
        }
        elseif ($q->urlType === 'post')
        {
            $this->post_request();
        }
        elseif ($q->urlType === 'get')
        {
            $this->get_request();
        }
        else
        {
            throw new Exception ('The query type is invalid.\n')
        }
    }

    private function delete_request()
    {
        curl_setopt($this->connection, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $data = curl_exec($this->connection);
        $json = check_curl($data);
    }

    private function get_request()
    {
        //Nothing to modify for get curl_setopts
        $data = curl_exec($this->connection);
        $json = check_curl($data);
    }

    private function post_request($body)
    {
        curl_setopt($this->connection, CURLOPT_POST, true);
        curl_setopt($this->connection, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($this->connection, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        $data = curl_exec($this->connection);
        $json = check_curl($data);

    }

    private function check_curl($data)
    {
        $http_status = curl_getinfo($this->connection, CURLINFO_HTTP_CODE);
        $this->close() /*close the curl connection*/
        if ($data == false)
        {
            throw new Exception("PHP/cURL has encountered an error: \n"."#".curl_error($this->connection);
        }

        $json = json_decode($data, true);

        switch (json_last_error())
        {
            case JSON_ERROR_DEPTH:
                throw new Exception('JSON ERROR: Maximum stack depth exceeded.');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception('JSON ERROR: Underflow or mode mismatch.');
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception('JSON ERROR: Unexpected control charachter found.');
                break;
            case JSON_ERROR_SYNTAX:
                throw new Exception('JSON ERROR: Syntax error, malformed JSON detected.');
                break;
            case JSON_ERROR_UTF8:
                throw new Exception('JSON ERROR: Malformed UTF-8 charachters detected, incorrect encoding possible.');
                break;
            default:
                break;
        }

        if ($http_status < 200 || $http_status > 300)
        {
            throw new Exception('HTTP ERROR: Received an unexpected http response: '.$http_status);
        }
        return ($json);
    }

    private function get_body($query)
    {
        $body = new stdClass();
        if (!$query)
        {
            return $body;
        }

        if ($query->params->debug !== undefined && $query->params->debug !== null)
        {
            $body->debug = $query->params->debug;
        }

        if ($query->base_url === '/alter_table')
        {
            if ($query->params->add_columns)
            {
                $body->add_columns = $query->params->add_columns;
            }
            if ($query->params->alter_columns)
            {
                $body->alter_columns = $query->params->alter_columns;
            }
            if ($query->params->name)
            {
                $body->name = $query->params->name;
            }
            if ($query->params->description)
            {
                $body->description = $query->params->description;
            }
            if ($query->params->drop_columns)
            {
                $body->drop_columns = $query->params->drop_columns;
            }
            if ($query->params->is_private)
            {
                $body->is_private = $query->params->is_private;
            }
            if ($query->params->license)
            {
                $body->license = $query->params->license;
            }
            if ($query->params->rename)
            {
                $body->rename = $query->params->rename;
            }
            if ($query->params->sources)
            {
                $body->sources = $query->params->sources;
            }
        }

        elseif ($query->base_url === '/create_table')
        {
            if ($query->params->columns)
            {
                $body->columns = $query->params->columns;
            }
            if ($query->params->name)
            {
                $body->name = $query->params->name;
            }
            if ($query->params->description)
            {
                $body->description = $query->params->description;
            }
            if ($query->params->is_private)
            {
                $body->is_private = $query->params->is_private;
            }
            if ($query->params->license)
            {
                $body->license = $query->params->license;
            }
            if ($query->params->sources)
            {
                $body->sources = $query->params->sources;
            }
        }

        elseif ($query->base_url === '/delete_from')
        {
            if ($query->params->name)
            {
                $body->name = $query->params->name;
            }
            if ($query->params->where)
            {
                $body->where = $query->params->where;
            }
        }

        elseif ($query->base_url === '/insert_into')
        {
            if ($query->params->name)
            {
                $body->name = $query->params->name;
            }
            if ($query->params->values)
            {
                $body->values = $query->params->values;
            }
        }

        elseif ($query->base_url === '/select_from')
        {
            if ($query->params->distinct !== undefined && $query->params->distinct !== null)
            {
                $body->distinct = $query->params->distinct;
            }
            if ($query->params->from)
            {
                $body->from = $query->params->from;
            }
            if ($query->params->group_by)
            {
                $body->group_by = $query->params->group_by;
            }
            if ($query->limit !== undefined && $query->params->distinct !== null)
            {
                $body->limit = $query->params->limit;
            }
            if ($query->params->offset !== undefined && $query->params->offset !== null)
            {
                $body->offset = $query->params->offset;
            }
            if ($query->order_by)
            {
                $body->order_by = $query->params->order_by;
            }
            if ($query->select)
            {
                $body->select = $query->params->select;
            }
            if ($query->params->total !== undefined && $query->params->total !== null)
            {
                $body->total = $query->params->total;
            }
            if ($query->params->where)
            {
                $body->where = $query->params->where;
            }
        }

        elseif ($query->base_url === '/update')
        {
            if ($query->params->name)
            {
                $body->name = $query->params->name;
            }
            if ($query->params->set)
            {
                $body->set = $query->params->set;
            }
            if ($query->params->where)
            {
                $body->where = $query->params->where;
            }
        }

        return($body);

    }




}

/////////////////////////////////////////////////////////////////////////////////////



try
{
    $client = new client('test', 'test_it', 'google.com', 4001);
    //$wetest = $client->modify_connection();
    $client->object_printer();
    $client->get_curl_info();
    $client->close();
}
catch (Exception $e)
{
    echo $e."\n";
}
?>