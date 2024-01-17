<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('roles')->delete();
        $role = Role::create([
            'name'          => 'administrador_de_sistema',
            'display_name'	=>	'Administrador de sistema',             
            'description'   =>  'Permite utilizar y administrar todos los modulos del sistema',
            'guard_name'    => 'api'
        ]);

        $permisos = Permission::all();
        foreach ($permisos as $permiso) {
           $role->givePermissionTo($permiso);
        }

        $role = Role::create([
            'name'          =>  'driver',
            'display_name'	=>	'Conductor',
            'description'   =>  'Permite a un conductor utilizar los modulos de la aplicacion', //verificar si puede ingresar al sistema para que pueda agregar sus datos
            'guard_name'    =>  'api'
         ]);

         $role = Role::create([
            'name'          =>  'administrador_empresa',
            'display_name'	=>	'Administrador de empresa',
            'description'   =>  'Permite ver los modulos necesarios para la operacion de la empresa',
            'guard_name'    =>  'api'
         ]);
    }
}
