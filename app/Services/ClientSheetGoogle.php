<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;

class ClientSheetGoogle
{
    private $client;

    // public static $serviceG;
    // private  $service;

    // public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Sheet Google API');
        $this->client->setAuthConfig(storage_path('credentials.json'));
        $this->client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setAccessType('offline');
        $this->service = new Google_Service_Sheets($this->client);
    }


    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */



    public function getService()
    {
        return $this->service;
    }

}
