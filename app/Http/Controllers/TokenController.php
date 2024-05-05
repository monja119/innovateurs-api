<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Token;
use App\Models\User;

class TokenController extends Controller
{
    public function create(string $user_id)
    {
        // verify if token exists and not expired
        $token = Token::where('user_id', $user_id)
            ->where('expired_at', '>', date('Y-m-d H:i:s'))
            ->first();
        if($token) {
            return $token;
        }

        Token::where('user_id', $user_id)->delete();
        $token_value = bin2hex(random_bytes(32));
        $expired_at = date('Y-m-d H:i:s', strtotime('+1 day'));
        $refresh_token = bin2hex(random_bytes(32));

        $token = Token::create([
            'token' => $token_value,
            'user_id' => $user_id,
            'expired_at' => $expired_at,
            'refresh_token' => $refresh_token
        ]);

        return $token;
    }

    public function refresh(string $refresh_token)
    {
        // delete all expired tokens
        Token::where('expired_at', '<', date('Y-m-d H:i:s'))->delete();

        $token = Token::where('refresh_token', $refresh_token)
            ->where('expired_at', '>', date('Y-m-d H:i:s'))
            ->first();

        if($token) {
            $token->expired_at = date('Y-m-d H:i:s', strtotime('+1 day'));
            $token->refresh_token = bin2hex(random_bytes(32));
            $token->save();
            return $token;
        }
        else {
            return response()->json(['message' => 'Token not found'], 404);
        }
    }

    public function verify(string $user_id)
    {
        Token::where('expired_at', '<', date('Y-m-d H:i:s'))->delete();

        $token = Token::where('user_id', $user_id)
            ->where('expired_at', '>', date('Y-m-d H:i:s'))
            ->first();

        if($token) {
            return $token;
        }
        return false;
    }


    public function verify_token(string $token)
    {
        Token::where('expired_at', '<', date('Y-m-d H:i:s'))->delete();

        $token = Token::where('token', $token)
            ->where('expired_at', '>', date('Y-m-d H:i:s'))
            ->first();

        if($token) {
            return $token;
        }
        return false;
    }


    public function delete(string $token)
    {
        $token = Token::where('token', $token)->first();

        if(!$token) {
            return false;
        }

        $token->delete();
        return true;
    }
}
