<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        // try {
        try {
            $user = Socialite::driver('google')->user();
            $finduser = User::where('email', $user->email)->first();
            if($finduser !== NULL){
                Auth()->login($finduser);
            }else{
                return redirect()->route('login');
            }
            return redirect()->route('home');
        }
        catch(Exception $e)
        {
            return redirect()->route('login');
        }


    }
}