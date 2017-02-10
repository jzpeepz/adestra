<?php

namespace Jzpeepz\Adestra;

class AdestraResponse {

    protected $rawResponse = null;

    public function __construct(\PhpXmlRpc\Response $response)
    {
        $this->rawResponse = $response;
    }

    public static function make(\PhpXmlRpc\Response $response)
    {
        return new AdestraResponse($response);
    }

    public function getData()
    {
        if (isset($this->rawResponse->val->me) && is_array($this->rawResponse->val->me) && isset($this->rawResponse->val->me['array'])) {
            $data = $this->rawResponse->val->me['array'];

            $newData = [];

            foreach ($data as $key => $object) {
                $newData[$key] = @array_shift(array_values($object->me));
            }

            return $newData;
        }

        return [];
    }

    public function isError()
    {
        if (isset($this->rawResponse->errno) && $this->rawResponse->errno != 0) {
            return true;
        }

        return false;
    }

}
