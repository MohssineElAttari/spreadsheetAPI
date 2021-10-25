<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use App\Services\ClientSheetGoogle;
use Google\Service\AdExchangeBuyerII\Size;
use Illuminate\Support\Facades\Auth;
use stdClass;

class SpreadSheetController extends Controller
{

    private $service;
    private $client;

     public function __construct(Google_Client $client)
    {
        $this->client = $client;
        $this->service = new \Google_Service_Sheets($client);
    //    dd();
    }
    //get SpreadsheetID from spreadsheet link.

    public function GetSpreadsheetID($link)
    {
        $Arraylink = explode("/", $link);
        $spreadsheetID = $Arraylink[5]; //It is present in your URL
        return $spreadsheetID;
    }
    //get list name sheet from spreadsheet.
    public function getRanges($spreadsheetID)
    {
        $spreadSheet = $this->service->spreadsheets->get($spreadsheetID);
        $sheets = $spreadSheet->getSheets();
        $sheetList = array();
        // $sheet->properties->sheetId
        foreach ($sheets as $sheet) {
            array_push($sheetList, $sheet->properties->title);
        }
        return $sheetList;
    }
    //Reading data from spreadsheet.
    // Fetching data from your spreadsheet and storing it.

    public function addSheet()
    {
        // dd($this->client->getAccessToken());
    }
    // public function testAuth()
    // {
    //     dd(auth('sanctum')->user());


    // }
    public function readSheet(Request $linkSheet)
    {
        // dd($this->getClient());

        $accessToken = [
            'access_token' => auth()->user()->token,
            'created' => auth()->user()->created_at->timestamp,
            'expires_in' => auth()->user()->expires_in,
            'refresh_token' => auth()->user()->refresh_token
        ];

        $this->client->setAccessToken($accessToken);

        if ($this->client->isAccessTokenExpired()) {
            // if ($this->client->getRefreshToken()) {
            //     $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            // }
            auth()->user()->update([
                'token' => $this->client->getAccessToken()['access_token'],
                'expires_in' => $this->client->getAccessToken()['expires_in'],
                'created_at' => $this->client->getAccessToken()['created'],
            ]);
        }

        // $this->client->refreshToken(auth()->user()->refresh_token);

        // $this->client->setAccessToken($accessToken);

        $spreadsheetID = "1JR2uAjnN67c4sRnfnyGXdzXjz535v6MNgB48pLvVI1I";
        $get_range = "P2m";

        $response = $this->service->spreadsheets_values->get($spreadsheetID, $get_range);
        $values = $response->getValues();
        dd($values);

        // dd($this->client->getAccessToken());
        $spreadsheetID = $this->GetSpreadsheetID($linkSheet->link);
        $get_range = $this->getRanges($spreadsheetID)[0];

        $this->client->setAccessToken(auth()->user()->token);
        //Request to get data from spreadsheet.
        $response = $this->service->spreadsheets_values->get($spreadsheetID, $get_range);
        $values = $response->getValues();


        $columns = $values[0];

        $list = array();
        for ($i = 0; $i < count($values); $i++) {
            if ($i == 0) continue;

            $value = $values[$i];
            // dd($value);
            $obj = new stdClass();
            for ($j = 0; $j < count($columns); $j++) {
                // dd($value[$j]);
                $obj->{$columns[$j]} = $value[$j]; //rda
            }
            array_push($list, $obj);
        }

        return response(["data" => $list]);
    }

    function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        // $client->setAuthConfig(storage_path('credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.

        $accessToken = [
            'access_token' => auth()->user()->token,
            'created' => auth()->user()->created_at->timestamp,
            'expires_in' => auth()->user()->expires_in,
            'refresh_token' => auth()->user()->refresh_token
        ];

        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            }
            auth()->user()->update([
                'token' => $this->client->getAccessToken()['access_token'],
                'expires_in' => $this->client->getAccessToken()['expires_in'],
                'created_at' => $this->client->getAccessToken()['created'],
            ]);
        }

        // // If there is no previous token or it's expired.
        // if ($client->isAccessTokenExpired()) {
        //     // Refresh the token if possible, else fetch a new one.
        //     if ($client->getRefreshToken()) {
        //         $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        //     } else {
        //         // Request authorization from the user.
        //         $authUrl = $client->createAuthUrl();
        //         printf("Open the following link in your browser:\n%s\n", $authUrl);
        //         print 'Enter verification code: ';
        //         $authCode = trim(fgets(STDIN));

        //         // Exchange authorization code for an access token.
        //         $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        //         $client->setAccessToken($accessToken);

        //         // Check to see if there was an error.
        //         if (array_key_exists('error', $accessToken)) {
        //             throw new Exception(join(', ', $accessToken));
        //         }
        // }
        // Save the token to a file.
        // if (!file_exists(dirname($tokenPath))) {
        //     mkdir(dirname($tokenPath), 0700, true);
        // }
        // file_put_contents($tokenPath, json_encode($client->getAccessToken()));

        return $client;
    }


    //add data in spreadSheet
    public function addRowInSheet(Request $request)
    {
        // dd($spreadsheetID);
        // $data =array_values($request);
        $data = collect($request);
        $flattened = $data->flatten()->toArray();

        // $values = [["new value Prenom", "new value nom", "new value ville"]];
        $get_range = $this->getRanges("1JR2uAjnN67c4sRnfnyGXdzXjz535v6MNgB48pLvVI1I")[0];

        // array_push($data, $sheet->properties->title);
        // Creating a request.
        $body = new Google_Service_Sheets_ValueRange([
            'values' => [$flattened],
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $insert = [
            "insertDataOption" => "INSERT_ROWS"
        ];
        // Calling add service.
        $this->service->spreadsheets_values->append("1JR2uAjnN67c4sRnfnyGXdzXjz535v6MNgB48pLvVI1I", $get_range, $body, $params, $insert);
        return response(["success" => true, "message" => "inserted  data succsufuly"]);
    }

    //update data in spreadSheet
    public function updateRowInSheet(Request $request)
    {
        $data = collect($request);
        $flattened = $data->flatten()->toArray();

        // $values = [["new value Prenom", "new value nom", "new value ville"]];
        $$update_range = $this->getRanges("1JR2uAjnN67c4sRnfnyGXdzXjz535v6MNgB48pLvVI1I")[0];

        // array_push($data, $sheet->properties->title);
        // Creating a request.
        $body = new Google_Service_Sheets_ValueRange([
            'values' => [$flattened],
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $insert = [
            "insertDataOption" => "INSERT_ROWS"
        ];
        // Calling add service.
        // Calling update service.

        $this->service->spreadsheets_values->update("1JR2uAjnN67c4sRnfnyGXdzXjz535v6MNgB48pLvVI1I", $update_range . '!A5:B5', $body, $params);
        return response(["success" => true, "message" => "inserted  data succsufuly"]);
    }
}
