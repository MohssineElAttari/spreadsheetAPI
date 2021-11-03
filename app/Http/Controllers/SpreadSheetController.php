<?php

namespace App\Http\Controllers;

use App\Models\Spread;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Illuminate\Support\Facades\Auth;
use stdClass;
use App\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class SpreadSheetController extends Controller
{

    protected $service;
    // protected $client;
    protected $user;
    public static $clientAPI;
    public function __construct(Google_Client $client)
    {
        $this->middleware(function ($request, $next) use ($client) {
            $this->user = Auth::user();
            // dd($this->user);
            $accessToken = [
                'access_token' => auth()->user()->token,
                'created' => auth()->user()->created_at->timestamp,
                'expires_in' => auth()->user()->expires_in,
                'refresh_token' => auth()->user()->refresh_token
            ];
            // dd($accessToken);
            $client->setAccessToken($accessToken);
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                }
                auth()->user()->update([
                    'token' =>  $client->getAccessToken()['access_token'],
                    'expires_in' =>  $client->getAccessToken()['expires_in'],
                    'created_at' =>  $client->getAccessToken()['created'],
                ]);
            }

            $client->refreshToken(auth()->user()->refresh_token);


            $this->service = new  Google_Service_Sheets($client);

            return $next($request);
        });
        SpreadSheetController::set_client_api($client);
        // SpreadSheetController::getClientAPI();
        // dd(SpreadSheetController::$clientAPI);
        //    dd();
    }
    //get SpreadsheetID from spreadsheet link.
    public static function set_client_api($clientapi)
    {
        SpreadSheetController::$clientAPI = $clientapi;
    }
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
    public function createApi(Request $linkSheet)
    {
        try {
            $spreadsheetID = $this->GetSpreadsheetID($linkSheet->all()['link']);
            $get_range = $this->getRanges($spreadsheetID)[0];

            if ($this->service->spreadsheets_values->get($spreadsheetID, $get_range)) {
                $registration_number = bin2hex(random_bytes(8));;
                $id = Auth::user()->id;
                $spread = Spread::find($spreadsheetID);
                // dd($spread);
                // if (!$spread) {
                //     return response([
                //         "Success" => false,
                //         'Message' => "Spraeds already exist"
                //     ]);
                // } else {
                Spread::create([
                    'user_id' => $id,
                    'registration_number' => $registration_number,
                    'spreadsheetID' => $spreadsheetID,
                ]);

                return redirect()->route('show')
                    ->with('success', 'spreads lists');
                // }
            }
        } catch (Exception $e) {
            return response([
                "Success" => false,
                'Message' => $e->getMessage()
            ]);
        }
    }

    public function ShowpreadSheet()
    {
        // dd('wata sir');
        $spreads = DB::table('spreads')
            ->join('users', 'users.id', '=', 'spreads.user_id')
            ->select('spreads.*')
            ->where('users.id', Auth::user()->id)
            ->get()->toArray();
        // $data['spreads'] = $spreads;
        // dd($spreads);
        return view('dashboard.spreadsheets')->with(['spreads' => $spreads]);
    }

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
        // dd($this->user);
        try {

            $spreadsheetID = $this->GetSpreadsheetID($linkSheet->all()['link']);
            $get_range = $this->getRanges($spreadsheetID)[0];

            if ($response = $this->service->spreadsheets_values->get($spreadsheetID, $get_range)) {
                $values = $response->getValues();
            }
        } catch (\Throwable $e) {
            return response([
                "Success" => false,
                'Message' => "We received an error that you don't have permission to this document."
            ]);
        }

        $columns = $values[0];
        dd($values[10]);
        $list = array();
        for ($i = 0; $i < count($values); $i++) {
            if ($i == 0) continue;

            $value = $values[$i];
            // dd($value);
            $obj = new stdClass();
            for ($j = 0; $j < count($columns); $j++) {
                $obj->{$columns[$j]} = $value[$j]; //rda
            }
            array_push($list, $obj);
        }
        return response([$get_range => $list]);
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
