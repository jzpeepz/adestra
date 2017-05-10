<?php

namespace Jzpeepz\Adestra;

class AdestraCampaign {

    public $id = null;
    protected $client = null;
    public $data = null;
    public $options = null;
    public $content = [];

    public function __construct($data = [], $debug = false)
    {
        $this->data = $data;

        // create adestra client
        $client = AdestraClient::make($debug);

        $this->client = $client;
    }

    public static function make($data = [], $debug = false)
    {
        return new AdestraCampaign($data, $debug);
    }

    public static function find($id, $debug = false)
    {
        $campaign = new AdestraCampaign([], $debug);

        $campaign->id = $id;

        return $campaign;
    }

    public function create()
    {
        $data = $this->data;

        // call the API to create the campaign
        // campaign.create(campaign_data)
        $response = $this->client->request('campaign.create', compact('data'));

        // get id from the complicated response :)
        $this->id = $response->val->me['struct']['id']->me['int'];

        // return campaign object
        return $this;
    }

    public function update($data = [])
    {
        $this->data = $data;
        $id = $this->id;

        // call the API to create the campaign
        // campaign.create(campaign_data)
        $response = $this->client->request('campaign.update', compact('id', 'data'));

        // return campaign object
        return $this;
    }


    public function setAllOptions($options = [])
    {
        $id = $this->id;
        $this->options = $options;

        // call API to set options
        // campaign.setAllOptions($this->id, campaign_options_data)
        $this->client->request('campaign.setAllOptions', compact('id', 'options'));

        return $this;
    }

    public function setMessage($format = 'html', $content = null)
    {
        $id = $this->id;
        $this->content[$format] = $content;

        // call API to set message
        // campaign.setMessage($this->id, $format, $content)
        $this->client->request('campaign.setMessage', compact('id', 'format', 'content'));

        return $this;
    }

    public function publish()
    {
        // publish the campaign
        // campaign.publish($this->id)
        $this->client->request('campaign.publish', ['id' => $this->id]);

        return $this;
    }

    public function launch($data = [])
    {
        $id = $this->id;
        $defaults = [
            'launch_label' => date('Y-m-d') . ' enews',
        ];

        $data = array_merge($defaults, $data);

        // launch campaign
        // campaign.launch($this->id, { launch_label: '2016-01-01 daily email' })
        $response = $this->client->request('campaign.launch', compact('id', 'data'));

        return AdestraResponse::make($response);
    }

    public function sendTest($email, $options = [])
    {
        $id = $this->id;

        // allow for comma delimited email addresses
        $emails = explode(',', str_replace(' ', '', $email));

        $response = $this->client->request('campaign.sendTest', compact('id', 'emails', 'options'));

        return AdestraResponse::make($response);
    }

}
