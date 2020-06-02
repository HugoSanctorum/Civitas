<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use App\Entity\Role;
use App\Entity\Permission;
use App\Repository\PermissionRepository;

class RoleFixtures extends Fixture implements DependentFixtureInterface
{
	private $permissionRepository;

	public function __construct(PermissionRepository $permissionRepository){
		$this->permissionRepository = $permissionRepository;
	}

	public function feed($role, $permissions){
		foreach ($permissions as $permission) {
			$role->addPermissions($permission);
		}
	}

    public function load(ObjectManager $manager)
    {
        $user = new Role();
        $technicien = new Role();
        $gestionnaire = new Role();
        $admin = new Role();

        $user->setRole("ROLE_USER");
        $technicien->setRole("ROLE_TECHNICIEN");
        $gestionnaire->setRole("ROLE_GESTIONNAIRE");
        $admin->setRole("ROLE_ADMIN");
        $this->addReference($admin->getRole(),$admin);
        $this->addReference($technicien->getRole(), $technicien);
        $this->addReference($gestionnaire->getRole(),$gestionnaire);
        $this->addReference($user->getRole(),$user);

		$userPermissions = [
			$this->getReference("GET_SELF_PROBLEME"),
/*			$this->getReference("POST_PROBLEME"),*/
/*			$this->getReference("UPDATE_SELF_PROBLEME"),*/
/*			$this->getReference("DELETE_SELF_PROBLEME"),*/

/*			$this->getReference("GET_SELF_PERSONNE"),*/
			$this->getReference("UPDATE_SELF_PERSONNE"),
			$this->getReference("DELETE_SELF_PERSONNE"),

			$this->getReference("GET_SELF_ROLE"),

			$this->getReference("GET_SELF_COMMUNE"),

			$this->getReference("GET_SELF_HISTORIQUE_ACTION"),
			$this->getReference("POST_HISTORIQUE_ACTION"),

			$this->getReference("GET_SELF_INTERVENTION"),

			$this->getReference("GET_SELF_IMAGE"),
			$this->getReference("POST_IMAGE"),
			$this->getReference("UPDATE_SELF_IMAGE"),
			$this->getReference("DELETE_SELF_IMAGE"),

			$this->getReference("GET_SELF_STATUT"),
			$this->getReference("GET_OTHER_STATUT"),

			$this->getReference("GET_SELF_HISTORIQUE_STATUT"),

			$this->getReference("GET_SELF_PRIORITE"),
			$this->getReference("POST_PRIORITE"),

			$this->getReference("GET_SELF_CATEGORIE"),
			$this->getReference("GET_OTHER_CATEGORIE")
		];

		$technicienPermissions = [
            $this->getReference("GET_INTERVENED_PROBLEME"),
            $this->getReference("GET_SELF_INTERVENTION"),
            $this->getReference("POST_INTERVENTION"),
            $this->getReference( "CAN_DO_INTERVENTION"),
            $this->getReference("GET_SELF_COMPTE_RENDU"),
            $this->getReference("POST_COMPTE_RENDU")
        ];

		$gestionnairePermissions = [
			$this->getReference("GET_OTHER_PROBLEME"),
			$this->getReference("UPDATE_OTHER_PROBLEME"),
			$this->getReference("DELETE_OTHER_PROBLEME"),
            $this->getReference("VALIDATE_PROBLEME"),
            
            //$this->getReference("ARCHIVATE_PROBLEME"),

            $this->getReference("CAN_EDIT_STATUT_PROBLEME"),

			$this->getReference("GET_OTHER_PERSONNE"),

			$this->getReference("GET_SELF_ROLE"),
			$this->getReference("GET_OTHER_ROLE"),

			$this->getReference("GET_SELF_COMMUNE"),

			$this->getReference("GET_SELF_HISTORIQUE_ACTION"),
			$this->getReference("GET_OTHER_HISTORIQUE_ACTION"),
			$this->getReference("POST_HISTORIQUE_ACTION"),

			$this->getReference("GET_SELF_INTERVENTION"),
			$this->getReference("GET_OTHER_INTERVENTION"),
			$this->getReference("POST_INTERVENTION"),

			$this->getReference("GET_OTHER_IMAGE"),

			$this->getReference("GET_SELF_SERVICE"),
			$this->getReference("GET_OTHER_SERVICE"),

			$this->getReference("GET_SELF_STATUT"),
        	$this->getReference("GET_OTHER_STATUT"),
        	$this->getReference("UPDATE_SELF_STATUT"),
        	$this->getReference("UPDATE_OTHER_STATUT"),

        	$this->getReference("GET_SELF_PRIORITE"),
        	$this->getReference("GET_OTHER_PRIORITE"),
        	$this->getReference("UPDATE_SELF_PRIORITE"),
        	$this->getReference("UPDATE_OTHER_PRIORITE"),

        	$this->getReference("GET_SELF_HISTORIQUE_STATUT"),
        	$this->getReference("GET_OTHER_HISTORIQUE_STATUT"),

        	$this->getReference("GET_SELF_CATEGORIE"),
        	$this->getReference("GET_OTHER_CATEGORIE"),
        	$this->getReference("POST_CATEGORIE"),
        	$this->getReference("UPDATE_SELF_CATEGORIE"),
        	$this->getReference("UPDATE_OTHER_CATEGORIE"),
        	$this->getReference("DELETE_SELF_CATEGORIE"),
        	$this->getReference("DELETE_OTHER_CATEGORIE")
        ];

		$adminPermissions = $this->permissionRepository->findAll();

		$this->feed($user, $userPermissions);
        $this->feed($technicien,$technicienPermissions);
		$this->feed($admin, $adminPermissions);
		$this->feed($gestionnaire, $gestionnairePermissions);


		$manager->persist($user);
		$manager->persist($technicien);
		$manager->persist($gestionnaire);
		$manager->persist($admin);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            PermissionFixtures::class
        );
    }

}
