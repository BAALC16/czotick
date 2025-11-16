<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'code'          => 'categorie.create',
                'nom'           => 'Créer',
                'description'   => 'Créer des Catégories',
                'section'       => 'Catégories'
            ], [
                'code'          => 'categorie.edit',
                'nom'           => 'Modifier',
                'description'   => 'Modifier des Catégories',
                'section'       => 'Catégories'
            ], [
                'code'          => 'categorie.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des Catégories',
                'section'       => 'Catégories'
            ], [
                'code'          => 'role.create',
                'nom'           => 'Créer',
                'description'   => 'Créer des Rôles',
                'section'       => 'Rôles'
            ], [
                'code'          => 'role.edit',
                'nom'           => 'Modifier',
                'description'   => 'Modifier des Rôles',
                'section'       => 'Rôles'
            ], [
                'code'          => 'role.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des Rôles',
                'section'       => 'Rôles'
            ], [
                'code'          => 'role.assign',
                'nom'           => 'Assigner',
                'description'   => 'Assigner des Rôles à des Utilisateurs',
                'section'       => 'Rôles'
            ], [
                'code'          => 'service.create',
                'nom'           => 'Ajouter',
                'description'   => 'Ajouter des Services',
                'section'       => 'Services'
            ], [
                'code'          => 'service.list',
                'nom'           => 'Lister',
                'description'   => 'Lister les Services',
                'section'       => 'Services'
            ], [
                'code'          => 'service.edit',
                'nom'           => 'Modifier',
                'description'   => 'Modifier des Services',
                'section'       => 'Services'
            ], [
                'code'          => 'service.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des Services',
                'section'       => 'Services'
            ], [
                'code'          => 'user.edit',
                'nom'           => 'Modifier',
                'description'   => 'Modifier les infos des Utilisateurs',
                'section'       => 'Utilisateurs'
            ], [
                'code'          => 'user.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des comptes Utilisateurs',
                'section'       => 'Utilisateurs'
            ], [
                'code'          => 'reservation.list',
                'nom'           => 'Lister toutes',
                'description'   => 'Lister les Réservations',
                'section'       => 'Réservations'
            ], [
                'code'          => 'reservation.validate',
                'nom'           => 'Approuver',
                'description'   => 'Accepter des Réservations',
                'section'       => 'Réservations'
            ], [
                'code'          => 'reservation.reject',
                'nom'           => 'Rejeter',
                'description'   => 'Rejeter des Réservations',
                'section'       => 'Réservations'
            ], [
                'code'          => 'reservation.assign',
                'nom'           => 'Assigner',
                'description'   => 'Assigner des Réservations à un Prestataire',
                'section'       => 'Réservations'
            ], [
                'code'          => 'reservation.edit',
                'nom'           => 'Modifier',
                'description'   => 'Modifier des Réservations',
                'section'       => 'Réservations'
            ], [
                'code'          => 'reservation.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des Réservations',
                'section'       => 'Réservations'
            ], [
                'code'          => 'annonce.create',
                'nom'           => 'Créer',
                'description'   => 'Créer des Annonces',
                'section'       => 'Annonces'
            ], [
                'code'          => 'annonce.edit',
                'nom'           => 'Modifier',
                'description'   => 'Modifier des Annonces',
                'section'       => 'Annonces'
            ], [
                'code'          => 'annonce.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des Annonces',
                'section'       => 'Annonces'
            ], [
                'code'          => 'annonce.reject',
                'nom'           => 'Rejeter',
                'description'   => 'Rejeter des Annonces',
                'section'       => 'Annonces'
            ], [
                'code'          => 'annonce.validate',
                'nom'           => 'Approuver',
                'description'   => 'Approuver des Annonces',
                'section'       => 'Annonces'
            ], [
                'code'          => 'devis.create',
                'nom'           => 'Créer',
                'description'   => 'Créer des Devis',
                'section'       => 'Devis'
            ], [
                'code'          => 'devis.list',
                'nom'           => 'Lister',
                'description'   => 'Lister les Devis',
                'section'       => 'Devis'
            ], [
                'code'          => 'devis.edit',
                'nom'           => 'Modifier',
                'description'   => 'Modifier des Devis',
                'section'       => 'Devis'
            ], [
                'code'          => 'devis.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des Devis',
                'section'       => 'Devis'
            ], [
                'code'          => 'devis.validate',
                'nom'           => 'Approuver',
                'description'   => 'Approuver des Devis',
                'section'       => 'Devis'
            ], [
                'code'          => 'devis.reject',
                'nom'           => 'Rejeter',
                'description'   => 'Rejeter des Devis',
                'section'       => 'Devis'
            ], [
                'code'          => 'avis.list',
                'nom'           => 'Lister',
                'description'   => 'Lister les Avis',
                'section'       => 'Avis'
            ], [
                'code'          => 'avis.validate',
                'nom'           => 'Approuver',
                'description'   => 'Approuver des Avis',
                'section'       => 'Avis'
            ], [
                'code'          => 'avis.reject',
                'nom'           => 'Rejeter',
                'description'   => 'Rejeter des Avis',
                'section'       => 'Avis'
            ], [
                'code'          => 'avis.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des Avis',
                'section'       => 'Avis'
            ],

            [
                'code'          => 'projet.create',
                'nom'           => 'Créer',
                'description'   => 'Créer des Projets',
                'section'       => 'Projets'
            ], [
                'code'          => 'projet.edit',
                'nom'           => 'Modifier',
                'description'   => 'Modifier des Projets',
                'section'       => 'Projets'
            ], [
                'code'          => 'projet.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des Projets',
                'section'       => 'Projets'
            ], [
                'code'          => 'projet.reject',
                'nom'           => 'Rejeter',
                'description'   => 'Rejeter des Projets',
                'section'       => 'Projets'
            ], [
                'code'          => 'projet.validate',
                'nom'           => 'Approuver',
                'description'   => 'Approuver des Projets',
                'section'       => 'Projets'
            ],

            [
                'code'          => 'properties.create',
                'nom'           => 'Créer',
                'description'   => 'Créer des Propriétés',
                'section'       => 'Propriétés'
            ],[
                'code'          => 'properties.list',
                'nom'           => 'Lister',
                'description'   => 'Lister les Propriétés',
                'section'       => 'Propriétés'
            ], [
                'code'          => 'properties.edit',
                'nom'           => 'Modifier',
                'description'   => 'Modifier des Propriétés',
                'section'       => 'Propriétés'
            ], [
                'code'          => 'properties.delete',
                'nom'           => 'Supprimer',
                'description'   => 'Supprimer des Propriétés',
                'section'       => 'Propriétés'
            ], [
                'code'          => 'properties.reject',
                'nom'           => 'Rejeter',
                'description'   => 'Rejeter des Propriétés',
                'section'       => 'Propriétés'
            ], [
                'code'          => 'properties.validate',
                'nom'           => 'Approuver',
                'description'   => 'Approuver des Propriétés',
                'section'       => 'Propriétés'
            ],
        ];

        foreach ($permissions as $p) {
          if(!(Permission::where('code', $p['code'])->exists())) {
            Permission::create($p);
          }
        }
    }
}
