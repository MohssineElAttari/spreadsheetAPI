<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Spread;
use App\Models\User;
use Google_Client;
use Google_Service_Sheets;
// use Google_Service_Sheets_ValueRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpreadController extends Controller
{
    protected $service;
    protected $client;
    // protected $client;
    protected $user;

    public function __construct()
    {
    }

    public function getClient($id)
    {
        // $user = User::find($id);
        $spread = Spread::where('registration_number', $id)->first();
        $user=User::find($spread->user_id);
        // $user = DB::table('users')
        //     ->join('spreads', 'users.id', '=', 'spreads.user_id')
        //     ->select('users.*')
        //     ->where('spreads.registration_number', $id)
        //     ->get();
        // dd($user);
        $accessToken = [
            'access_token' => $user->token,
            'created' => $user->created_at->timestamp,
            'expires_in' => $user->expires_in,
            'refresh_token' => $user->refresh_token
        ];
        // dd($accessToken);
        $this->client = new Google_Client();
        $this->client->setAccessToken($accessToken);
        dd($this->client);
        return  $this->client;
    }

    public function showData($id)
    {
        // dd($id);
        $this->client = $this->getClient($id);
        // dd($this->client);
        $this->service = new  Google_Service_Sheets($this->client);
        $spreadsheetID = "1JR2uAjnN67c4sRnfnyGXdzXjz535v6MNgB48pLvVI1I";
        $get_range = "P2m";

        if ($response = $this->service->spreadsheets_values->get($spreadsheetID, $get_range)) {
            $values = $response->getValues();
        }
        // dd($values);
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
}
