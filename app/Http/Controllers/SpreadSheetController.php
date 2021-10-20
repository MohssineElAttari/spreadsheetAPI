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
    public function __construct()
    {
        $clientSG = new ClientSheetGoogle;
        $this->client = $clientSG->getClient();
        $this->service = $clientSG->getService();
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
    public function testAuth()
    {
        if(Auth::check()){
            $user = Auth::user();
            dd($user->id);
        }else {
            dd("walo ahamadi");
        }
       
       
    }
    public function readSheet(Request $linkSheet)
    {
       

        // $arr_token = (array) ;
        $accessToken = array(
            'access_token' => $arr_token['access_token'],
            'expires_in' => $arr_token['expires_in'],
        );


        dd($this->client->getAccessToken());
        $spreadsheetID = $this->GetSpreadsheetID($linkSheet->link);
        $get_range = $this->getRanges($spreadsheetID)[0];

        $this->client->setAccessToken('ya29.a0ARrdaM9IfBTfDTBIsQk8Qh8vuMg9z6anvEbB7VZbjLFoRKx7cv9_AwBDWI-AmzIZErst3tkSB1AHkcUiToITofUaGONUHEzdVVZL3lzEJ0YZ9e8xcUV5oHwliiGKqP35dDr6EpPHZtiJ557rmMkOmMWGGeON');
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
