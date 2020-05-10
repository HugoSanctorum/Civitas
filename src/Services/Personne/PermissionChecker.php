<?php

namespace App\Services\Personne;

use App\Entity\Personne;
use App\Entity\Role;
use App\Entity\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PermissionChecker{
	
	private $em;
	private $user;
	private $personneRepository;

	public function  __construct(
		EntityManagerInterface $entityManager,
		TokenStorageInterface $tokenStorageInterface
	){
		$this->em = $entityManager;
		$this->user = $tokenStorageInterface->getToken()->getUser();
	}

	public function isUserGranted(Array $permissions){
		foreach($permissions as $permission){
			foreach ($this->user->getUserRoles() as $role) {
				foreach ($role->getPermissions() as $valid_permission) {
					if($permission == $valid_permission->getPermission()){
						return true;
					}
				}
			}
		}
		return false;
	}

	public function isUserGrantedSelf(Array $permissions, boolean $self){
		if($self) $this->isUserGranted();
	}
}