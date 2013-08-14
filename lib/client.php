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
require('client_debug.php')

class client 
{
    private $auth_key;
    private $auth_secret;
    private $url;
    private $verify_ssl;
    private $name = 'client_constructor'

    /*
    *THIS CONSTRUCT CREATES A BASIC OBJECT WITH ATTRIBUTES NECESSARY TO
    *INTERACT WITH PROPREITARY DATALANCHE DATA PROCESSING FUNCTIONALITY
    */
    public function __construct($key = '', $secret = '', $host = NULL, $port = '', $verify_ssl = True)
    {
        //Variable fallback overrides
        $this->host = 'api.datalanche.com';
        $this->port = NULL;
        $this->$verify_ssl = True;
        
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
            $runtime_warnings->append(new runtime_error(debug_backtrace(), $name))
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
        $url = $url.$host;
            if($port != NULL)
            {
                $url = $url.':'.(string)$port;
            }
        $connection = curl_init();
        //ASSSIGN CURL INFO
            curl_setopt($connection, CURLOPT_CUSTOMREQUEST, 'DELETE');
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
            curl_setopt($connection, CURLOPT_URL, $url);
    }
    

}

try
{
    $client = new client('test', 'test_it', 'sdkfhsdkjfhjdh');
    var_dump($client);
}
catch (Exception $e)
{
    echo $e."\n";
}
?>