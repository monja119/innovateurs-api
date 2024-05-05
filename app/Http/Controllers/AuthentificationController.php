<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Token;
use Exception;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TokenController;

class AuthentificationController extends Controller
{
    public function __construct()
    {
        $this->user_controller = new UserController();
        $this->token_controller = new TokenController();
    }

    public function connexion($email, $password)
    {
        $user = User::where('email', $email)->first();
        if($user->status == 0)
            return [
                'connection' => false,
                'message' => "Votre compte n'est pas encore activé"
            ];

        if($user) {
            if(Hash::check($password, $user->password)) {
                return [
                    'connection' => true,
                    'user' => $user
                ];
            }
            else {
                return [
                    'connection' => false,
                    'message' => 'Mot de passe incorrect'
                ];
            }
        }
        else {
            return [
                'connection' => false,
                'message' => 'Utilisateur non trouvé'
            ];
        }

    }


    public function login(Request $request)
    {

        try {
            $loginData = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $code = 400;
            return response()->json(
                [
                    'message' => $message,
                    'code' => $code,
                    'methode' => $request->method(),
                ]
                , $code
            );
        }

        $connexion = $this->connexion($loginData['email'], $loginData['password']);

        if (!$connexion['connection']) {
            $response = [
                'message' => $connexion['message'],
                'code' => 200,
                'methode' => $request->method(),
                'connection' => $connexion
            ];

            return response($response, 200);
        }
        else
        {
            $message = "Connexion réussie";
            $user = $connexion['user'];
            $token = $this->token_controller->verify($user->id);
            if(!$token)
                $token = $this->token_controller->create($user->id);
            $user->remember_token = $token->refresh_token;

            $response = [
                'message' => $message,
                'code' => 200,
                'methode' => $request->method(),
                'connection' => $connexion,
                'user' => $user,
                'token' => $token
            ];
        }
        return response($response, 202);
    }


    public function logout(string $token)
    {
        $token = $this->token_controller->delete($token);

        if($token) {
            return response()->json(['message' => 'Déconnexion réussie'], 200);
        }
        else {
            return response()->json(['message' => 'Token non trouvé'], 404);
        }
    }
}
