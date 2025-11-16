<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use DB;
use Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!(Role::count())) {
            $permissions = Permission::all();
            // Fondateur
            $founder = new Role([
                'nom' => "Fondateur",
                'description' => "Propriétaire Fondateur du site. Privilèges illimités sur l'ensemble du système",
                'icone' => 'far fa-user-tie',
                'backoffice_route' => 'admin',
            ]);
            $founder->save();
            // Toutes les permissions
            DB::table('roles_permissions')->insert($permissions->map(function($p) use ($founder) {
                return [
                    'role_id' => $founder->id,
                    'permission_id' => $p->id,
                ];
            })->toArray());

            // Administrateur
            $adm = new Role([
                'nom' => 'Administrateur',
                'description' => "Membres de l'équipe d'administration du système.",
                'icone' => 'far fa-user-shield',
                'backoffice_route' => 'admin',
            ]);
            $adm->save();
            // Toutes les permissions, sauf sur les roles
            DB::table('roles_permissions')->insert(
                ($permissions->filter(function($p) {
                    return !Str::startsWith($p->code, 'role.');
                }))->map(function($_p) use ($adm) {
                    return [
                        'role_id' => $adm->id,
                        'permission_id' => $_p->id,
                    ];
                })->toArray()
            );
        }
    }
}
