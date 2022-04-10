<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Session;

use Str;
use Hash;

class LoginController extends Controller
{
    //
    public function login(Request $request) {
        $rules = [
            'email'         => 'required|string|email|min:3|max:255',
            'password'      => 'required|string|min:3|max:255',
        ];

        logger()->info('LoginController::login()');

        logger()->info('$request->all(): ' . json_encode($request->all()));

        $validator = $this->validate($request, $rules);

        logger()->info('LoginController::login() - validator: ' . print_r($validator, true));

        if($validator) {
            $user = User::where(['email' => $request->input('email')])
                                ->select('id','first_name', 'last_name', 'email', 'password')
                                ->get();
            $user = (count($user) > 0) ? $user[0] : null;

            if($user) {
                if(Hash::check($request->input('password'), $user->password)) {
                    $session = new Session();
                    $session->user_id = $user->id;
                    $session->session_id = Str::random(48);
                    $session->expires_at = now()->addMinutes(config('session.lifetime'));
                    $session->save();

                    logger()->info('LoginController::login() - user: ' . print_r($user, true));

                    return response()->json([
                        'message' => 'Login success',
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
                        ],
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Login failed - different passwords',
                    ], 401);
                }
            } else {
                logger()->info('LoginController::login() - user not found');
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
        } else {
            logger()->info('LoginController::login() - login failed');
            return response()->json([
                'message' => 'Login failed',
            ], 401);
        }
    }
}
