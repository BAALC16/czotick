<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
Use DB;
Use Str;
Use Hash;

class AddRolesMembreAndPrestataireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!(Role::where('nom', 'Membre')->exists())) {
          $permissions = Permission::all();
          // Prestataire
          $presta = new Role([
              'nom' => 'Prestataire',
              'description' => "Prestataires de services sur demande.",
              'icone' => 'far fa-user-friends',
              'backoffice_route' => 'prestataire',
          ]);
          $presta->save();
          // Toutes les permissions, sauf sur les roles
          DB::table('roles_permissions')->insert(
              ($permissions->filter(function($p) {
                  return ($p->code == "devis.create" ||
                    $p->code == "properties.list" ||
                    $p->code == "properties.create" ||
                    $p->code == "reservation.list" ||
                    $p->code == "reservation.create");
              }))->map(function($_p) use ($presta) {
                  return [
                      'role_id' => $presta->id,
                      'permission_id' => $_p->id,
                  ];
              })->toArray()
          );

          // Redacteur
        $editor = new Role([
            'nom' => 'Redacteur',
            'description' => "Redacteur d'articles.",
            'icone' => 'far fa-pencil',
            'backoffice_route' => 'article',
        ]);
        $editor->save();
        // Toutes les permissions, sauf sur les roles
        DB::table('roles_permissions')->insert(
            ($permissions->filter(function($p) {
                return ($p->code == "annonce.create" ||
                  $p->code == "annonce.edit" ||
                  $p->code == "annonce.delete");
            }))->map(function($_p) use ($editor) {
                return [
                    'role_id' => $editor->id,
                    'permission_id' => $_p->id,
                ];
            })->toArray()
        );

          // Membre
          $member = new Role([
              'nom' => 'Membre',
              'description' => "Utilisateurs inscrits sur le site.",
              'icone' => 'far fa-users',
              'backoffice_route' => 'membre',
          ]);
          $member->save();


          // Create default super-admin (Founder)
          $email = "info@mck.immo";
          $pwd = "q1w2e3r4"; //Str::random(8);
          $user = new User([
            'email' => $email,
            'password' => Hash::make($pwd),
            'nom' => 'MCK',
            'prenoms' => 'Immo',
            'titre' => 'Administrateur',
            'ville' => 'Abidjan',
            'code_pays' => 'CI',
            'email_pro' => 'info@mck.immo',
            'mobile' => '+2250574012162',
            'site_web' => 'https://mck.immo',
          ]);
          $user->saveQuietly();
          $roles = Role::whereIn('nom', ['Fondateur', 'Administrateur', 'Membre'])->get();
          $roles->each(function($role) use($user) {
            $user->roles()->save($role);
          });
          echo "\n\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n\n";
          echo "\t COMPTE ADMINISTRATEUR PRINCIPAL \n\n";
          echo "\t email : $email \n";
          echo "\t mot de passe : $pwd\n";
          echo "\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n";


          // Create default super-admin (Founder)
          $email = "cybertoc@yahoo.fr";
          $pwd = "q1w2e3r4"; //Str::random(8);
          $user = new User([
            'email' => $email,
            'password' => Hash::make($pwd),
            'nom' => 'Prestataire',
            'prenoms' => 'Agent',
            'titre' => 'Prestataire',
            'ville' => 'Abidjan',
            'code_pays' => 'CI',
            'email_pro' => 'cybertoc@yahoo.fr',
            'mobile' => '+2250501010101',
            'site_web' => 'https://mck.immo',
          ]);
          $user->saveQuietly();
          $roles = Role::whereIn('nom', ['Prestataire', 'Membre'])->get();
          $roles->each(function($role) use($user) {
            $user->roles()->save($role);
          });
          echo "\n\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n\n";
          echo "\t COMPTE PRESTATAIRE \n\n";
          echo "\t email : $email \n";
          echo "\t mot de passe : $pwd\n";
          echo "\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n";
        }
    }
}
