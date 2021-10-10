<?php
	
	namespace App\Utilities;
	
	use App\Entity\User;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
	use Symfony\Component\Security\Core\Security;
	
	class GestionSecurity
	{
		private $_em;
		private $request;
		private $security;
		private $hasher;
		
		public function __construct(EntityManagerInterface $_em, RequestStack $request, Security $security, UserPasswordHasherInterface $hasher)
		{
			$this->_em = $_em;
			$this->request = $request;
			$this->security = $security;
			$this->hasher = $hasher;
		}
		
		/**
		 * Initialisation du compte SUPER ADMINISTRATEUR
		 *
		 * @return bool
		 */
		public function intialisation(): bool
		{
			$user = $this->_em->getRepository(User::class)->findOneBy(['username'=>'delrodie']);
			$result = false;
			if (!$user){
				$user = new User();
				$user->setEmail('delrodieamoikon@gmail.com');
				$user->setUsername('delrodie');
				$user->setPassword($this->hasher->hashPassword($user, 'gesform'));
				$user->setRoles(['ROLE_SUPER_ADMIN']);
				$this->_em->persist($user);
				$this->_em->flush();
				
				$result = true;
			}
			
			return $result;
		}
		
		/**
		 * Incrementation du nombre de connexion et de la derniere date
		 *
		 * @return bool
		 */
		public function connexion(): bool
		{
			$user = $this->_em->getRepository(User::class)->findOneBy(['username'=>$this->security->getUser()->getUserIdentifier()]);
			
			// Definition des variable
			$nombre_conneion = (int)$user->getLoginCount();
			
			$user->setLoginCount($nombre_conneion + 1);
			$user->setLastConnectedAt(new \DateTime()); //dd($user);
			
			$this->_em->flush();
			
			return true;
		}
	}
