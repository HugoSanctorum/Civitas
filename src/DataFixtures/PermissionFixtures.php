<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Permission;

class PermissionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $permissions = [
        	["GET_SELF_PROBLEME","Voir ses problèmes"],
        	["GET_OTHER_PROBLEME","Voir tous les problèmes"],
        	//"POST_PROBLEME", //Un compte anonyme ne possede pas de role, donc tout le monde peut creer un probleme
        	//"UPDATE_SELF_PROBLEME", //Non utilisable
        	["UPDATE_OTHER_PROBLEME","Modifier tous les problèmes"],
        	//"DELETE_SELF_PROBLEME",//''
            ["DELETE_OTHER_PROBLEME","Supprimer des problèmes"],
            ["VALIDATE_PROBLEME","Valider un problème"],
/*            "ARCHIVATE_PROBLEME",*/
            ["CAN_EDIT_STATUT_PROBLEME","Modifier le statut d'un problème"],
            ["GET_INTERVENED_PROBLEME","Intervenir sur un problème"],

/*        	["GET_SELF_PERSONNE",],*/
        	["GET_OTHER_PERSONNE","Voir la liste des utilisateurs"],
/*        	["POST_PERSONNE","Créer un utilisateur"],*/
        	["UPDATE_SELF_PERSONNE","Modifier son compte"],
        	["UPDATE_OTHER_PERSONNE","Modifier les comptes des autres utilisateurs"],
        	["DELETE_SELF_PERSONNE","Supprimer son compte"],
        	["DELETE_OTHER_PERSONNE","Supprimer les utilisateurs"],

        	["GET_SELF_ROLE","Voir ses rôles"],
        	["GET_OTHER_ROLE","Voir les rôles des utilisateurs"],
        	["POST_ROLE","Ajouter un rôle"],
        	["UPDATE_SELF_ROLE","Modifier ses rôles"],
        	["UPDATE_OTHER_ROLE","Modifier les rôles et les permissions des utilisateurs"],
        	["DELETE_SELF_ROLE","Supprimer ses roles"],
        	["DELETE_OTHER_ROLE","Supprimer les rôles des utilisateurs"],

            ["GET_SELF_COMMUNE","Voir sa commune"],
            ["GET_OTHER_COMMUNE","Voir la commune des utilisateurs"],
            ["POST_COMMUNE","Ajouter une commune"],
/*            ["UPDATE_SELF_COMMUNE","Modifier sa commune"],*/
            ["UPDATE_OTHER_COMMUNE","Modifier la commune des utilisateurs"],
/*            ["DELETE_SELF_COMMUNE",],*/
            ["DELETE_OTHER_COMMUNE","Supprimer la commune des utilisateurs"],

        	/*["GET_SELF_HISTORIQUE_ACTION",],
        	["GET_OTHER_HISTORIQUE_ACTION",],
        	["POST_HISTORIQUE_ACTION",],
        	["UPDATE_SELF_HISTORIQUE_ACTION",],
        	["UPDATE_OTHER_HISTORIQUE_ACTION",],
        	["DELETE_SELF_HISTORIQUE_ACTION",],
        	["DELETE_OTHER_HISTORIQUE_ACTION",],*/

        	["GET_SELF_INTERVENTION","Voir ses interventions"],
        	["GET_OTHER_INTERVENTION","Voir les interventions des utilisateurs"],
        	["POST_INTERVENTION","Creer une intervention"],
        	["UPDATE_SELF_INTERVENTION","Modifier ses interventions"],
        	["UPDATE_OTHER_INTERVENTION","Modifier les interventions des utilisateurs"],
       /* 	["DELETE_SELF_INTERVENTION","Supprimer ses interventions"],
        	["DELETE_OTHER_INTERVENTION","Supprimer les interventions des autres utilisateurs"],*/

        	/*["GET_SELF_IMAGE",""],
        	["GET_OTHER_IMAGE",],
        	["POST_IMAGE",],
        	["UPDATE_SELF_IMAGE",],
        	["UPDATE_OTHER_IMAGE",],
        	["DELETE_SELF_IMAGE",],
        	["DELETE_OTHER_IMAGE",],*/

        	["GET_SELF_SERVICE","Voir les services de sa commune"],
        	["GET_OTHER_SERVICE","Voir les services des utilisateurs"],
        	["POST_SERVICE","Creer un service"],
        	["UPDATE_SELF_SERVICE","Modifier les services de sa commune"],
        	["UPDATE_OTHER_SERVICE","Modifier les services des utilisateurs"],
        	["DELETE_SELF_SERVICE","Supprimer les services de sa commune"],
        	["DELETE_OTHER_SERVICE","Supprimer les services des utilisateurs"],

        	["GET_OTHER_STATUT", "Voir la liste des statuts possibles"],
        	["POST_STATUT", "Créer un statut de problème"],
        	["UPDATE_OTHER_STATUT","Modifier un statut"],
        	["DELETE_OTHER_STATUT","Supprimer un statut"],


        	["GET_OTHER_PRIORITE","Voir la liste des priorités possibles"],
        	["POST_PRIORITE","Creer une priorité"],
        	["UPDATE_OTHER_PRIORITE","Modifier une priorité"],
        	["DELETE_OTHER_PRIORITE","Supprimer une priorité"],

/*        	["GET_SELF_HISTORIQUE_STATUT","Voir les statuts de son problème"],*/
        	["GET_OTHER_HISTORIQUE_STATUT","Voir les statuts de tous les problèmes"],
        	["POST_HISTORIQUE_STATUT", "Modifier le statut d'un problème"],
/*        	["UPDATE_SELF_HISTORIQUE_STATUT",],*/
            ["UPDATE_OTHER_HISTORIQUE_STATUT","Modifier le statut de tous les problèmes"],
        	/*["DELETE_SELF_HISTORIQUE_STATUT",],
        	["DELETE_OTHER_HISTORIQUE_STATUT",],*/

        	["GET_OTHER_CATEGORIE","Voir la liste des catégories possibles"],
        	["POST_CATEGORIE","Creer une catégorie"],
        	["UPDATE_OTHER_CATEGORIE","Modifier une catégorie"],
        	["DELETE_OTHER_CATEGORIE","Supprimer une catégorie"],

            ["GET_SELF_COMPTE_RENDU","Voir ses comptes-rendus"],
            ["GET_OTHER_COMPTE_RENDU","Voir tous les comptes-rendus"],
            ["POST_COMPTE_RENDU","Creer un compte-rendu"],
            ["UPDATE_SELF_COMPTE_RENDU","Modifier ses comptes-rendus"],
            ["UPDATE_OTHER_COMPTE_RENDU","Modifier tous les comptes-rendus"],
/*            ["DELETE_SELF_COMPTE_RENDU","Supprimer ses comptes-rendus"],*/
/*            ["DELETE_OTHER_COMPTE_RENDU","Supprimer tous les comptes-rendus"],*/
        ];

        foreach ($permissions as $permission) {
        	$object = new Permission();
        	$object->setPermission($permission[0]);
        	$object->setLabel($permission[1]);
            $this->addReference($object->getPermission(), $object);
        	$manager->persist($object);
        }

        $manager->flush();
    }
}
