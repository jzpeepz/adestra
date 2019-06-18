<?php

namespace Jzpeepz\Adestra;

class AdestraImport
{
    public $id = null;
    protected $client = null;
    public $data = null;

    public function __construct($data = [], $debug = false)
    {
        $this->data = $data;

        // create adestra client
        $client = AdestraClient::make($debug);

        $this->client = $client;
    }

    public static function make($data = [], $debug = false)
    {
        return new static($data, $debug);
    }

    public function create()
    {
        // import_id = import.create(create)
        $response = $this->client->request('import.create', [
            'create' => $this->data
        ]);

        // set the id from response
        if (isset($response->val) && isset($response->val->me)) {
            $this->id = $this->data['id'] = @array_shift(array_values($response->val->me));
        }

        return $this;
    }
}
