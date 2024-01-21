<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Sites;
use App\Models\Driver;
use App\Models\DriverAddress;
use App\Models\Clients;
use App\Models\ClientsUsers;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('users')->delete();        
        
        $user = User::create([
            'names'=>'Rogelio',
            'lastname1'=>'Gamez',
            'lastname2'=>'',
            'email'=>'rogelio@logistic.com',
            'role'=>'administrador_de_sistema',
            'password'=>bcrypt('s0p0rt3'),
            'change_password'=>true
        ]);

        $permisos = Permission::all();
        foreach ($permisos as $permiso) {
        	//asigna permiso a usuario
            $user->givePermissionTo($permiso->name);
            //$rol->givePermissionTo($permission);
     	}

        $driver = Driver::create([
            'users_id'=>$user->id,
            'names'=>'Rogelio',
            'lastname1'=>'Gamez',
            'lastname2'=>'',
            'status'=>'Activo'
        ]);

        $driverAddress = DriverAddress::create([
            'drivers_id'=>$driver->id,
            'street'=>'Carlos Tovar',
            'ext_number'=>'311',
            'colony'=>'Centro',
            'state'=>'San Luis Potosí',
            'municipality'=>'San Luis Potosí',
            'zip_code'=>'78000',
            'isFiscal'=>true
        ]);

        $clients = Clients::all();
        foreach($clients as $client){
            $clientUSer = ClientsUSers::create([
                'clients_id'=>$client->id,
                'users_id'=>$user->id
            ]);
        }

     	//asigna rol a usuario
        $user->assignRole('administrador_de_sistema');


        $user = User::create([
            'names'=>'Edgar',
            'lastname1'=>'Mendoza',
            'lastname2'=>'',
            'email'=>'edgar@logistic.com',
            'role'=>'administrador_de_sistema',
            'password'=>bcrypt('s0p0rt3'),
            'change_password'=>true
        ]);       

        $permisos = Permission::all();
        foreach ($permisos as $permiso) {
        	//asigna permiso a usuario
            $user->givePermissionTo($permiso->name);
            //$rol->givePermissionTo($permission);
     	}

         $driver = Driver::create([
            'users_id'=>$user->id,
            'names'=>'Edgar',
            'lastname1'=>'Mendoza',
            'lastname2'=>'',
            'status'=>'Activo'
        ]);

        $driverAddress = DriverAddress::create([
            'drivers_id'=>$driver->id,
            'street'=>'Paseo de la Estepa',
            'ext_number'=>'221',
            'colony'=>'Puerta Natura',
            'state'=>'San Luis Potosí',
            'municipality'=>'San Luis Potosí',
            'zip_code'=>'78397',
            'isFiscal'=>true
        ]);

        $clients = Clients::all();
        foreach($clients as $client){
            $clientUSer = ClientsUSers::create([
                'clients_id'=>$client->id,
                'users_id'=>$user->id
            ]);
        }

     	//asigna rol a usuario
        $user->assignRole('administrador_de_sistema');
        
    }
}
