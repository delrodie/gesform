<?php
	
	namespace App\Utilities;
	
	use App\Entity\Candidat;
	use App\Entity\Sygesca\Membre;
	use Doctrine\ORM\EntityManagerInterface;
	
	class GestionCandidature
	{
		private $_em;
		private $_photo;
		
		public function __construct(EntityManagerInterface $_em, GestionPhoto $_photo)
		{
			$this->_em = $_em;
			$this->_photo = $_photo;
		}
		
		public function formulaire($request,$scout)
		{
			$mediaFile = $request->files->get('scout_photo');
			$media = null;
			if ($mediaFile)
				$media = $this->_photo->upload($mediaFile, 'photo');
			
			
			$candidat = new Candidat();
			$candidat->setMatricule($scout->getMatricule());
			$candidat->setNom($scout->getNom());
			$candidat->setPrenoms($scout->getPrenoms());
			$candidat->setDateNaissance($scout->getDatenaissance());
			$candidat->setLieuNaissance($scout->getLieunaissance());
			$candidat->setCarteScoute($scout->getCarte());
			$candidat->setFonction($scout->getFonction());
			$candidat->setSexe($scout->getSexe());
			$candidat->setSlug($scout->getSlug());
			$candidat->setRegion($scout->getGroupe()->getDistrict()->getRegion());
			$candidat->setDateEntree($request->get('scout_date_entree'));
			$candidat->setPhoto($media);
			
			$this->_em->persist($candidat);
			$this->_em->flush();
			
			return $candidat;
			
		}
		
		
		/**
		 * Fonction pour arrondir au supérieur
		 *
		 * @param $nombre
		 * @param $arrondi
		 * @return float|int
		 */
		public function arrondiSuperieur($nombre, $arrondi)
		{
			return ceil($nombre / $arrondi) * $arrondi;
		}
		
		/**
		 * fonction verification des valeurs
		 *
		 * @param $donnee
		 * @return string
		 */
		public function validForm($donnee)
		{
			$result = htmlspecialchars(stripslashes(trim($donnee)));
			
			return $result;
		}
		
	}
