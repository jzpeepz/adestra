<?php

namespace Jzpeepz\Adestra;

class AdestraClient {

    protected $xmlrpc = null;

    public function __construct($account, $username, $password, $debug = false)
    {
        $xmlrpc = new \PhpXmlRpc\Client("http://$account.$username:$password@app.adestra.com/api/xmlrpc");
        $xmlrpc->setDebug($debug);

        $this->xmlrpc = $xmlrpc;
    }

    public static function make($debug = false)
    {
        $account = config('adestra.account');
        $username = config('adestra.username');
        $password = config('adestra.password');

        return new AdestraClient($account, $username, $password, $debug);
    }

    public function request($endpoint, $params = [])
    {
        $options = static::arrayToValues($params);

        // _d($options); exit;

        $msg = new \PhpXmlRpc\Request($endpoint, $options);

        // echo '<xmp>' . print_r($msg->serialize(), true) . '</xmp>';

        return $this->xmlrpc->send($msg);
    }

    public static function arrayToValues($array = [])
    {
        $values = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = static::arrayToValues($value);
                $values[$key] = new \PhpXmlRpc\Value($value, self::isAssociative($value) ? 'struct' : 'array');
            }

            if (is_numeric($value)) {
                $values[$key] = new \PhpXmlRpc\Value($value, 'int');
            }

            if (is_string($value)) {
                $values[$key] = new \PhpXmlRpc\Value($value, 'string');
            }

            if (is_bool($value)) {
                $values[$key] = new \PhpXmlRpc\Value($value, 'boolean');
            }
        }

        return $values;
    }

    public static function isAssociative($arr)
    {
        if (! is_array($arr)) {
            return false;
        }

        return ! isset($arr[0]);
    }

}
