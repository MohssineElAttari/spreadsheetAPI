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
        // return Socialite::driver('google')->with($parameters)->redirect();
        // return Socialite::driver('google')->scopes(["https://www.googleapis.com/auth/spreadsheets"])->with($parameters)->redirect();

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            //create a user using socialite driver google
            $user = Socialite::driver('google')->user();
            // dd($user);
            $finduser = User::where('google_id', $user->id)->first();
            // dd($finduser);

            if ($finduser) {
                //if the user exists, login and show dashboard
                Auth::login($finduser, true);
                return redirect('/dashboard');
            } else {
                $newUser = User::create(
                    [
                        'email' => $user->email,
                        'google_id' => $user->id,
                        'password' => encrypt(''),
                        'avatar' => $user->avatar,
                        'token' => $user->token,
                        'expires_in' => $user->expiresIn,
                        'name' => $user->name,
                    ]
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
