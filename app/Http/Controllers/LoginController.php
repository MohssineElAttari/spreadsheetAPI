<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{

    public function redirectToGoogle()
    {
        // $parameters = [
        //     'access_type' => 'offline',
        //     'approval_prompt' => 'force'
        // ];
        // return Socialite::driver('google')->with($parameters)->redirect();
        $parameters = [
            'access_type' => 'offline',
            'approval_prompt' => 'force'
        ];
 
        return Socialite::driver('google')->scopes([\Google_Service_Sheets::SPREADSHEETS])->with($parameters)->redirect();

        // return Socialite::driver('google')->redirect();
        // return Socialite::driver('google')
        //     ->stateless()
        //     ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            //create a user using socialite driver google
            // dd("voila user");
            $auth_user = Socialite::driver('google')->stateless()->user();
            // dd($auth_user);
            $data = [
                'token' => $auth_user->token,
                'expires_in' => $auth_user->expiresIn,
                'name' => $auth_user->name,
                'google_id' => $auth_user->id,
                'password' => encrypt(''),
                'avatar' => $auth_user->avatar,
            ];
            if ($auth_user->refreshToken) {
                $data['refresh_token'] = $auth_user->refreshToken;
            }

            $user = User::updateOrCreate(
                [
                    'email' => $auth_user->email,
                ],
                $data
            );
            // dd($user);
            Auth::login($user, true);
            return redirect()->to('/dashboard'); // Redirect to a secure page

            // $user = Socialite::driver('google')->stateless()->user();
            // // dd("wawawa user");
            // $finduser = User::where('google_id', $user->id)->first();
            // // dd($finduser);
            // // dd($user);
            // if ($finduser) {
            //     //if the user exists, login and show dashboard
            //     Auth::login($finduser, true);
            //     return redirect('/dashboard');
            // } else {
            //     $newUser = User::create(
            //         [
            //             'email' => $user->email,
            //             'google_id' => $user->id,
            //             'password' => encrypt(''),
            //             'avatar' => $user->avatar,
            //             'token' => $user->token,
            //             'expires_in' => $user->expiresIn,
            //             'name' => $user->name,
            //         ]
            //     );
            //     $newUser->save();
            //     //login as the new user
            //     Auth::login($newUser, true);
            //     // go to the dashboard
            //     // dd(Auth::user());
            //     return redirect('/dashboard');
            // }
            //catch exceptions
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
