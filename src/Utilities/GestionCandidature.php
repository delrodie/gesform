<?php
	
	namespace App\Utilities;
	
	use App\Entity\Candidat;
	use App\Entity\Formation;
	use App\Entity\Sygesca\Membre;
	use App\Entity\Sygesca\Region;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Form\Form;
	
	class GestionCandidature
	{
		private $_em;
		private $_photo;
		
		public function __construct(EntityManagerInterface $_em, GestionPhoto $_photo)
		{
			$this->_em = $_em;
			$this->_photo = $_photo;
		}
		
		/**
		 * @param $request
		 * @param $scout
		 * @return Candidat
		 */
		public function formulaire($request,$scout): Candidat
		{
			$mediaFile = $request->files->get('scout_photo');
			$media = null;
			if ($mediaFile)
				$media = $this->_photo->upload($mediaFile, 'photo');
			
			$region = $this->_em->getRepository(Region::class)->findOneBy(['id'=>$scout->getGroupe()->getDistrict()->getRegion()->getId()]);
			
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
			$candidat->setRegion($region);
			$candidat->setDateEntree($this->validForm($request->get('scout_date_entree')));
			$candidat->setPhoto($media);
			$candidat->setFlag(1);
			
			$this->_em->persist($candidat);
			$this->_em->flush();
			
			return $candidat;
			
		}
		
		/**
		 * @param $request
		 * @param $candidat
		 * @return mixed
		 */
		public function personnelle($request, $candidat)
		{
			$candidat->setNiveauEtude($this->validForm($request->get('scout_niveau_etude')));
			$candidat->setProfession($this->validForm($request->get('scout_profession')));
			$candidat->setResidence($this->validForm($request->get('scout_residence')));
			$candidat->setemail($this->validForm($request->get('scout_email')));
			$candidat->setContact($this->validForm($request->get('scout_contact')));
			$candidat->setFlag(2);
			
			$this->_em->flush();
			
			return $candidat;
		}
		
		public function stagiaire($request, $candidat)
		{
			$mediaFile = $request->files->get('scout_stagiaire_media');
			$media = null;
			if ($mediaFile)
				$media = $this->_photo->upload($mediaFile, 'media');
			
			$formation = new Formation();
			$formation->setCandidat($candidat);
			$formation->setMedia($media);
			$formation->setType('STAGIAIRE');
			$formation->setNom($this->validForm($request->get('scout_stagiaire_nom')));
			$formation->setDate($this->validForm($request->get('scout_stagiaire_date')));
			$formation->setLieu($this->validForm($request->get('scout_stagiaire_lieu')));
			$formation->setTitularisation($this->validForm($request->get('scout_stagiaire_titularisation')));
			
			$this->_em->persist($formation);
			$this->_em->flush();
			
			$formations = $this->_em->getRepository(Formation::class)->findBy(['candidat'=>$candidat->getId()]);
			if (count($formations) > 1){
				$candidat->setFlag(3);
				$this->_em->flush();
			}
			
			return $formation;
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
