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
        $parameters = [
            'access_type' => 'offline',
            'approval_prompt' => 'force'
        ];
        return Socialite::driver('google')->scopes(["https://www.googleapis.com/auth/drive"])->with($parameters)->redirect();
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            //create a user using socialite driver google
            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();

            $data = [
                'token' => $finduser->token,
                'expires_in' => $finduser->expiresIn,
                'name' => $finduser->name
            ];

            if ($finduser->refreshToken) {
                $data['refresh_token'] = $finduser->refreshToken;
            }
            if ($finduser) {
                //if the user exists, login and show dashboard
                Auth::login($finduser, true);
                dd(Auth::user());
                // return redirect('/dashboard');
            } else {
                //user is not yet created, so create first
                $newUser = User::create(
                    [
                        'name' => $user->name,
                        'email' => $user->email,
                        'google_id' => $user->id,
                        'password' => encrypt(''),
                        'avatar' => $user->avatar,
                    ],
                    $data
                );
                $newUser->save();
                //login as the new user
                Auth::login($newUser, true);
                // go to the dashboard
                dd(Auth::user());
            }
            //catch exceptions
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
