<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Models\User;
use App\Models\UsersDetails;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $logged_id = auth()->user()->id;
        
        $admin = User::where('id', $logged_id)->select('role')->first();
        $all = true;
        if($admin->role !== 'administrador_de_sistema'){
            $all = false;
        }

        $users = User::join('roles', 'roles.name', 'users.role')
                    ->where('users.id', '!=', $logged_id)
                    ->where('active', 1);
        
        if($all){
            $users =  $users->selectRaw(
                            'users.names, users.lastname1, users.lastname2, 
                            users.email, 
                            roles.display_name as role, 
                            users.id'
                        )
                        ->get();
        }else{
            $users = $users->where('role', '!=', 'administrador_de_sistema')
                        ->selectRaw(
                            'users.names, users.lastname1, users.lastname2, 
                            users.email, 
                            roles.display_name as role, 
                            users.id'
                        )
                        ->get();
        }
                    
        
        return response()->json($users);
    }

    public function edit($id){
        $user = User::join('roles', 'roles.name', 'users.role')
                    ->where('users.id', $id)
                    ->select(
                        'users.names', 'users.lastname1', 'users.lastname2', 'users.email',                        
                        'users.role as role', 'users.id', 'users.active'
                    )
                    ->first();

        return response()->json($user);
    }

    public function store(Request $request){
        
        try{
            \DB::beginTransaction();
            $user = new User();
            $user->password = bcrypt($request->password);
            $user->fill($request->all());
            $user->save();
            
            $user->assignRole($request->role);
            
            $role = Role::select('id')->where('name', $request->role)->first();
     
            $role->permissions->each(function($p) use($user){
                $user->givePermissionTo($p->name);
            });

            \DB::commit();
            
            return response()->json([
                'ok' => true,
                'mensaje' => 'El empleado '.$user->names.' '.$user->lastname1.' '.$user->lastname2.' se registro correctamente.'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();      
         
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage().' line '.$e->getLine()]);
        }
    }

    public function update($id, Request $request){
        try{
            \DB::beginTransaction();
            $user = User::where('id', $id)->first();

            //remover permisos y role
            $role = Role::select('id')->where('name', $user->role)->first();

            $role->permissions->each(function($p) use($user){
                $user->revokePermissionTo($p->name);
            });

            $user->removeRole($user->role);
            $user->fill($request->all());
            $user->save();
            

            //Reasignar permisos y rol
            $user->assignRole($request->role);

            $role = Role::select('id')->where('name', $request->role)->first();
           
            $role->permissions->each(function($p) use($user){
                $user->givePermissionTo($p->name);
            });

            \DB::commit();
            
            return response()->json([
                'ok' => true,
                'mensaje' => 'El usuario '.$user->name.' se actualizo correctamente.'
            ]);
            
        }catch(\Exception $e){
            \DB::rollback();
            //dd($e);
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage()]);
        }
    }

    public function delete($id){
        try{
            \DB::beginTransaction();
            $user = User::find($id);
            $user->active = false;
            $user->save();

            \DB::commit();

            return response()->json([
                'mensaje'=>"Usuario ".$user->name." se elimino con exito."
            ]);
        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['error'=>'ERROR ('.$e->getCode().'): '.$e->getMessage()]);
        }
    }

    public function resetPassword(Request $request){
        $user_id = $request->userId;
        $password = $request->password;

        $user = User::find($user_id);
        $user->password = bcrypt($password);

        if(!$user->change_password){
            $user->change_password = true;
        }

        $user->save();

        return response()->json(['message'=>'Success']);
    }
}
