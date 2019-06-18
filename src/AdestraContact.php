<?php

namespace Jzpeepz\Adestra;

class AdestraContact
{
    public $id = null;
    protected $client = null;
    public $data = null;
    private $table_id = null;
    // public $options = null;
    // public $content = [];

    public function __construct($data = [], $debug = false)
    {
        // create adestra client
        $client = AdestraClient::make($debug);

        $this->client = $client;
        $this->data = $data;
        $this->table_id = config('adestra.core_table_id');

        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
    }

    public static function make($data = [], $debug = false)
    {
        return new AdestraContact($data, $debug);
    }

    public static function findByEmail($email)
    {
        $client = AdestraClient::make();

        $table_id = $this->table_id;
        $search_args = ['email' => $email];

        $response = $client->request('contact.search', compact('table_id', 'search_args'));

        $value = $response->val->me['array'];

        if (empty($value)) {
            // nothing found
            return null;
        } elseif (is_array($value) && count($value) == 1) {
            // only one found
            return self::makeFromPhpXmlRpcValue($value[0]);
        } else {
            // return array of results
            return $value;
        }
    }

    public static function makeFromPhpXmlRpcValue($value)
    {
        $data = [];

        // get data from value object
        if (get_class($value) == 'PhpXmlRpc\Value') {
            if (isset($value->me) && isset($value->me['struct'])) {
                $values = $value->me['struct'];

                foreach ($values as $name => $field) {
                    $data[$name] = @array_shift(array_values($field->me));
                }
            }
        }

        return self::make($data);
    }

    public function create($data = [])
    {
        $this->data = array_merge($this->data, $data);

        //contact.create(table_id, contact_data, dedupe_field)
        $response = $this->client->request('contact.create', [
            'table_id' => $this->table_id, 'contact_data' => $this->data
        ]);

        // set the id from response
        if (isset($response->val) && isset($response->val->me)) {
            $this->id = $this->data['id'] = @array_shift(array_values($response->val->me));
        }
    }

    public function update($data = [])
    {
        $this->data = array_merge($this->data, $data);

        //contact.create(table_id, contact_data, dedupe_field)
        $response = $this->client->request('contact.update', ['contact_id' => $this->id, 'contact_data' => $data]);

        // set the id from response
        if (isset($response->val) && isset($response->val->me)) {
            $this->id = $this->data['id'] = @array_shift(array_values($response->val->me));
        }
    }

    public function exists()
    {
        return (! empty($this->id));
    }

    public function subscribe($listId)
    {
        // verify contact existence
        if (! $this->exists()) {
            $this->create();
        }

        // contact.addList(contact_id, list_id)
        $response = $this->client->request('contact.addList', ['contact_id' => $this->id, 'list_id' => $listId]);

        $result = null;

        if (isset($response->val) && isset($response->val->me)) {
            $result = @array_shift(array_values($response->val->me));
        }

        return $result == 1;
    }

    public function unsubscribe($listId)
    {
        // verify contact existence
        if (! $this->exists()) {
            $this->create();
        }

        // contact.removeList(contact_id, list_id)
        $response = $this->client->request('contact.removeList', ['contact_id' => $this->id, 'list_id' => $listId]);

        $result = null;

        if (isset($response->val) && isset($response->val->me)) {
            $result = @array_shift(array_values($response->val->me));
        }

        return $result == 1;
    }

    public function lists()
    {
        if (! $this->exists()) {
            $this->create();
        }

        // contact.lists(id)
        $response = $this->client->request('contact.lists', ['id' => $this->id]);

        return AdestraResponse::make($response)->getData();
    }

    public function table($id)
    {
        $this->table_id = $id;

        return $this;
    }
}
