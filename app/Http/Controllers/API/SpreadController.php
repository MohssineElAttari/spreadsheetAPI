<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SpreadSheetController;
use App\Models\Spread;
use App\Models\User;
use Google_Client;
use Google_Service_Sheets;
// use Google_Service_Sheets_ValueRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use stdClass;

class SpreadController extends Controller
{
    protected $service;
    protected $client;
    // protected $client;
    protected $user;

    public function getClient($id)
    {
        $spread = Spread::where('registration_number', $id)->first();
        $user = User::find($spread->user_id);

        $client = new Google_Client();
        $client->setAuthConfig(Storage::path('client_secret.json'));
        $accessToken = [
            'access_token' => $user->token,
            'created' => $user->created_at->timestamp,
            'expires_in' => $user->expires_in,
            'refresh_token' => $user->refresh_token
        ];
        $client->setAccessToken($accessToken);
        return $client;
    }

    public function showData($id)
    {
        $spread = Spread::where('registration_number', $id)->first();

        $this->service = new  Google_Service_Sheets($this->getClient($id));
        $spreadsheetID = $spread->spreadsheetID;
        $get_range = "A:Z";

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
                $obj->{$columns[$j]} = $value[$j] ?? ""; //rda
            }
            array_push($list, $obj);
        }
        return response($list);
    }
}
