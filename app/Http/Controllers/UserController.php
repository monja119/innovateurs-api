<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\TokenController;

class UserController extends Controller
{
    public function __construct()
    {
        $this->token_controller = new TokenController();
    }
    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|max:55',
                'username' => 'required|max:55',
            ]);

            // verify if user already exists
            $user = User::where('email', $validatedData['email'])->first();
            if($user) {
                return response()->json([
                    'message' => "Utilisateur déjà existant avec cet email"
                ], 200);
            }

            $user = User::create([
                'email' => $validatedData['email'],
                'name' => $validatedData['username'],
            ]) ;

            $token = $this->token_controller->create($user->id);
            return response()->json([
                'user' => $user,
                'token' => $token
            ], 201);

        } catch (Exception $e) {
            $message = $e->getMessage();
            $code = 200;
            return response()->json([
                'message' => $message
            ], $code);
        }
    }

    public function show($token)
    {
        $token = $this->token_controller->verify_token($token);
        if($token) {
            $user = User::find($token->user_id);
            return response()->json([
                'user' => $user,
                'token' => $token
            ], 200);
        }
        else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        info('at update', $user);
        $user->update($request->all());
        return response()->json($user, 200);
    }

    public function activate(Request $request, $id)
    {
        $user = User::find($id);
        User::where('id', $id)->update(['status' => 1]);
        return response()->json($user, 200);
    }

    public function deactivate(Request $request, $id)
    {
        $user = User::find($id);
        User::where('id', $id)->update(['status' => 0]);
        return response()->json($user, 200);
    }


    public function delete($id)
    {
        User::destroy($id);
        return response()->json(null, 204);
    }

    public function index()
    {
        $users = User::all();

        return response()->json([
            'users' => $users
        ], 200);
    }


}
