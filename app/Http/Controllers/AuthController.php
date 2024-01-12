<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request){

        $request->validate([
            'email'     => 'required|string|email',
            'password'  => 'required|string'
        ]);

        $credentials = request(['email', 'password']);

        if(!\Auth::attempt($credentials)){
             return response()->json([
                 'error'     => 'Unauthorized',
                 'message'   => 'Usuario y/o contraseña incorrecto'
             ], 401);
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if($request->remember_be){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        //obtain user permissions
        $permissions = array();
        
        foreach($user->permissions as $p){            
            array_push($permissions, $p->name);
        };

        return response()->json([
            'access_token'  => $tokenResult->accessToken,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->names.' '.$user->lastname1.' '.(is_null($user->lastname2) ? '' : $user->lastname2),
                'change' => $user->change_password
            ],
            //permissions object
            'permissions' => $permissions,
            'expires_at'    => Carbon::parse($token->expires_at)->toDateString()
        ]);
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function register(Request $request){
        try{
            \DB::beginTransaction();

            $user = new User();
            $user->names = 'Rogelio';
            $user->lastname1 = 'Gamez';
            $user->lastname2 = 'Hernandez';
            $user->email = 'sistemas@mail.com';
            $user->password = bcrypt('s0p0rt3');
            $user->save();

            \DB::commit();

            return response()->json([
                'message' => 'Usuario registrado correctamente'
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'error' => 'ERROR ('.$e->getCode().'): '.$e->getMessage().', '.$e->getLine()                
            ], 401);
        }
    }
}
