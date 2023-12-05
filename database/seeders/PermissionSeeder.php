<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('permissions')->delete();


        \DB::table('permissions')->insert([
            'name'          => 'admin_menu',
            'display_name'  => 'Menú Admin',
            'description'   => 'Permite ver el menu de gestión de permisos, roles y usuarios.',
            'guard_name'	=> 'api'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'admin_permissions',
            'display_name'  => 'Listado de permisos',
            'description'   => 'Permite ver el listado de los permisos registrados en el sistema.',
            'guard_name'	=> 'api'
        ]);


        \DB::table('permissions')->insert([
            'name'          => 'admin_roles',
            'display_name'  => 'Listado Roles',
            'description'   => 'Permite ver el listado de roles que se encuentran en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'admin_permissions_assign',
            'display_name'  => 'Asignar permisos a rol',
            'description'   => 'Permite asignar permisos a un rol.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'admin_roles_create',
            'display_name'  => 'Registro de roles',
            'description'   => 'Permite registrar un nuevo rol en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'admin_roles_update',
            'display_name'  => 'Edición de roles',
            'description'   => 'Permite actualizar un rol en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'admin_roles_delete',
            'display_name'  => 'Eliminar roles',
            'description'   => 'Permite eliminar un rol en el sistema.',
            'guard_name'	=> 'api'
        ]);

        \DB::table('permissions')->insert([
            'name'          => 'admin_users',
            'display_name'  => 'Listado usuarios',
            'description'   => 'Permite ver el listado de usuarios que se encuentran en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'admin_users_create',
            'display_name'  => 'Registro de usuarios',
            'description'   => 'Permite registrar un nuevo usuario en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'admin_users_update',
            'display_name'  => 'Edición de usuarios',
            'description'   => 'Permite actualizar un usuario en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'admin_users_delete',
            'display_name'  => 'Eliminar usuarios',
            'description'   => 'Permite eliminar un usuario en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'admin_users_change_password',
            'display_name'  => 'Actualizar contraseña de usuarios',
            'description'   => 'Permite actualizar la contraseña de un usuario en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'admin_users_role_assign',
            'display_name'  => 'Asignar rol a usuario',
            'description'   => 'Permite asignar un rol a un usuario desde el formulario.',
            'guard_name'	=> 'api'
        ]);

        /** Driver */
        \DB::table('permissions')->insert([
            'name'          => 'drivers',
            'display_name'  => 'Listado conductores',
            'description'   => 'Permite ver el listado de conductores que se encuentran en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'drivers_create',
            'display_name'  => 'Registro de conductor',
            'description'   => 'Permite registrar un nuevo conductor en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'drivers_update',
            'display_name'  => 'Edición de conductor',
            'description'   => 'Permite actualizar un conductor en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'drivers_delete',
            'display_name'  => 'Eliminar conductor',
            'description'   => 'Permite eliminar un conductor en el sistema.',
            'guard_name'	=> 'api'
        ]);

        /** Bodega */
        \DB::table('permissions')->insert([
            'name'          => 'warehouse',
            'display_name'  => 'Listado bodegas',
            'description'   => 'Permite ver el listado de bodegas que se encuentran en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'warehouse_create',
            'display_name'  => 'Registro de bodega',
            'description'   => 'Permite registrar un nuevo bodega en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'warehouse_update',
            'display_name'  => 'Edición de bodega',
            'description'   => 'Permite actualizar un bodega en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'warehouse_delete',
            'display_name'  => 'Eliminar bodega',
            'description'   => 'Permite eliminar un bodega en el sistema.',
            'guard_name'	=> 'api'
        ]);

        /** Servicios */
        /** Estados */
        \DB::table('permissions')->insert([
            'name'          => 'services_states',
            'display_name'  => 'Listado estados',
            'description'   => 'Permite ver el listado de estados que se encuentran en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'services_states_load',
            'display_name'  => 'Carga de estados',
            'description'   => 'Permite carga todos los estados desde un archivp al sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'services_states_location',
            'display_name'  => 'Cargar ubicación',
            'description'   => 'Permite cargar la unicacion de los códigos postales de un municipio.',
            'guard_name'	=> 'api'
        ]);

         /** Servicios */
         \DB::table('permissions')->insert([
            'name'          => 'services',
            'display_name'  => 'Listado servicios',
            'description'   => 'Permite ver el listado de servicios que se encuentran en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'services_create',
            'display_name'  => 'Registro de servicio',
            'description'   => 'Permite registrar los servicios en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'services_update',
            'display_name'  => 'Edición de servicio',
            'description'   => 'Permite actualizar manualmente un servicio en el sistema.',
            'guard_name'	=> 'api'
        ]);
        \DB::table('permissions')->insert([
            'name'          => 'services_delete',
            'display_name'  => 'Eliminar servicio',
            'description'   => 'Permite eliminar un servicio en el sistema.',
            'guard_name'	=> 'api'
        ]);

        /** Reportes */
        \DB::table('permissions')->insert([
            'name'          => 'reports',
            'display_name'  => 'Listado de reportes',
            'description'   => 'Permite ver los diferentes reportes del sistema.',
            'guard_name'	=> 'api'
        ]);

    }
}
