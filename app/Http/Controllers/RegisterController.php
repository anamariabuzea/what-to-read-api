<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Session;

use Str;
use Hash;

class RegisterController extends Controller
{
    //
    public function register(Request $request) {
        $rules = [
            'first_name'    => 'required|string|min:3|max:255',
            'last_name'     => 'required|string|min:3|max:255',
            'email'         => 'required|string|email|min:3|max:255|unique:users',
            'password'      => 'required|confirmed|string|min:3|max:255',
        ];

        logger()->info('RegisterController::register()');

        logger()->info('$request->all(): ' . json_encode($request->all()));

        $validator = $this->validate($request, $rules);

        logger()->info('RegisterController::register() - validator: ' . print_r($validator, true));

        if($validator) {
            $user = new User();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->uuid = Str::uuid();
            $user->password = Hash::make($request->input('password'));
            $user->save();

            $session = new Session();
            $session->user_id = $user->id;
            $session->session_id = Str::random(48);
            $session->expires_at = now()->addMinutes(config('session.lifetime'));
            $session->save();

            logger()->info('RegisterController::register() - user: ' . print_r($user, true));

            return response()->json([
                'message' => 'Register success',
                'data'    => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                    ],
                    'session' => [
                        'session_id' => $session->session_id,
                        'expires_at' => $session->expires_at,
                    ],
                ]
            ]);
        } else {

            logger()->info('RegisterController::register() - validation failed');

            return response()->json([
                'message' => 'Register failed',
            ]);
        }
    }
}
