<?php

namespace App\Service\Personne;

use App\Entity\Personne;
use App\Entity\Role;
use App\Entity\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PermissionChecker(){
	
	private $em;
	private $user;
	private $personneRepository;

	public function  __construct(
		EntityManagerInterface $entityManager,
		TokenStorageInterface $tokenStorageInterface,
	){
		$this->em = $entityManager;
		$this->user = $tokenStorageInterface->getToken()->getUser();
	}

	public function isUserGranted(Permission $permission){
		foreach ($this->user->getRoles() as $role) {
			foreach ($role->getPermissions() as $valid_permission) {
				if($permission == $valid_permission->getPermission()){
					dump($permission + " / " + $valid_permission->getPermission());
				}
			}
		}
	}

}