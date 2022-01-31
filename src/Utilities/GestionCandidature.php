<?php
	
	namespace App\Utilities;
	
	use App\Entity\Activite;
	use App\Entity\Candidat;
	use App\Entity\Candidater;
	use App\Entity\Formation;
	use App\Entity\Sygesca\Membre;
	use App\Entity\Sygesca\Region;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Form\Form;
	
	class GestionCandidature
	{
		private $_em;
		private $_photo;
		private $_mail;
		
		public function __construct(EntityManagerInterface $_em, GestionPhoto $_photo, GestionMail $_mail)
		{
			$this->_em = $_em;
			$this->_photo = $_photo;
			$this->_mail = $_mail;
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
		
		public function formation($request, $candidat, $data)
		{
			$mediaFile = $request->files->get('scout_stagiaire_media');
			$media = null;
			if ($mediaFile)
				$media = $this->_photo->upload($mediaFile, 'media');
			
			$formation = new Formation();
			$formation->setCandidat($candidat);
			$formation->setMedia($media);
			$formation->setType($data['type']);
			$formation->setNom($this->validForm($request->get('scout_stagiaire_nom')));
			$formation->setDate($this->validForm($request->get('scout_stagiaire_date')));
			$formation->setLieu($this->validForm($request->get('scout_stagiaire_lieu')));
			$formation->setTitularisation($this->validForm($request->get('scout_stagiaire_titularisation')));
			
			$this->_em->persist($formation);
			$this->_em->flush();
			
			$formations = $this->_em->getRepository(Formation::class)->findBy(['candidat'=>$candidat->getId()]);
			if (count($formations) > 1){
				$candidat->setFlag($data['flag']);
				$this->_em->flush();
			}
			
			return $formation;
		}
		
		/**
		 * Paiement cinetpay
		 *
		 * @param $data
		 * @return Candidater|mixed|object|null
		 */
		public function cinetpay($data)
		{
			$candidate = $this->_em->getRepository(Candidater::class)->findOneBy(['id'=>$data['candidate']]);
			$candidate->setResponseId($data['response_id']);
			$candidate->setToken($data['token']);
			$candidate->setUrl($data['url']);
			$this->_em->flush();
			
			return $candidate;
		}
		
		/**
		 * @param $data
		 * @return Candidater|mixed|object|null
		 */
		public function cinetpay_acompte($data)
		{ //dd($data['candidate']);
			$candidate = $this->_em->getRepository(Candidater::class)->findOneBy(['id'=>$data['candidate']]);
			$candidate->setResponseIdAcompte($data['response_id']);
			$candidate->setTokenAcompte($data['token']);
			$candidate->setUrlAcompte($data['url']);
			$this->_em->flush();
			
			return $candidate;
		}
		
		/**
		 * @param $request
		 * @param $candidat
		 * @return Candidater
		 * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
		 */
		public function validation($request, $candidat): Candidater
		{ //dd();
			// Variables
			//$id_transaction = time().''.substr(uniqid("",true), -9, 9);
			$id_transaction = $request->server->get('REQUEST_TIME_FLOAT');
			$status_paiement = "ENCOURS";
			
			$date = date('Y-m-d', time());
			$activite = $this->_em->getRepository(Activite::class)->findEncours($date);
			
			$acompte = (int)$activite->getMontant() / 2;
			
			$montant = $this->montantAPayer($acompte);
			
			// Gestion du numero de dossier
			$nombre_candidat = count($this->_em->getRepository(Candidater::class)->findBy(['activite'=>$activite->getId()]));
			$code = $this->code($activite, $nombre_candidat+1); //dd($code);
			
			$candidater = new Candidater();
			$candidater->setCandidat($candidat);
			$candidater->setActivite($activite);
			$candidater->setIdTransaction($id_transaction);
			$candidater->setStatusPaiement($status_paiement);
			$candidater->setMontant($montant);
			$candidater->setAcompte($montant);
			
			$this->_em->persist($candidater);
			$this->_em->flush();
			
			$candidat->setFlag(5);
			$this->_em->flush();
			
			//$this->_mail->demande($candidater);
			
			return $candidater;
		}
		
		/**
		 * @param $matricule
		 * @return array|false
		 */
		public function existenceCandidat($matricule)
		{
			$candidat = $this->_em->getRepository(Candidat::class)->findOneBy(['matricule'=>$matricule]);
			if ($candidat){
				$flag = $candidat->getFlag();
				switch ($flag){
					case 1:
						$res = 'app_inscription_personnelle';
						break;
					/*case 2:
						$res = 'app_inscription_formation_stagiaire';
						break;
					case 3:
						$res = 'app_inscription_formation_formateur';
						break;*/
					default:
						$res = 'app_inscription_formation_stagiaire';
						break;
				}
				
				$route = [
					'route' => $res,
					'slug' => $candidat->getSlug()
				];
				return $route;
			}
			
			return false;
		}
		
		public function verifCandidature($candidat, $date)
		{
			$activite = $this->_em->getRepository(Activite::class)->findEncours($date);
			$candidater = $this->_em->getRepository(Candidater::class)->findOneBy(['candidat'=>$candidat->getId(), 'activite'=>$activite->getId()]); //dd($candidater);
			if ($candidater)
				if ($candidater->getValidation() && $candidater->getStatusPaiement()=== 'ENCOURS'){
					$message = 'votre candidature a été approuvée. Merci de proceder au paiement';
					return $message;
				}elseif(!$candidater->getValidation()){
					if ($candidater->getMention() === 'REJETER'){
						//$message = "Votre dossier a été rejété. Merci d'attendre à la prochaine session";
						return false;
					}elseif($candidater->getMention() === 'DOSSIER INCOMPLET'){
						$message = "Votre dossier est incomplet. Merci de l'actualiser";
					}else{
						$message = "Votre dossier en encours de traitement";
					}
					return $message;
				}else{
					return true;
				}
			else
				return false;
		}
		
		public function fincance($activite=null)
		{
			$candidats = $this->_em->getRepository(Candidater::class)->findParticipantByActiviteOrNo($activite);
			$data=[];$i=0; $montant_total=0; $montant=0;
			foreach ($candidats as $candidat){
				$age = date('Y') - date('Y', strtotime($candidat->getCandidat()->getDateNaissance()));
				$montant=(int) $candidat->getMontant() + (int) $candidat->getAcompte();
				$data[$i++] = [
					'code' => $candidat->getCode(),
					'matricule' => $candidat->getCandidat()->getMatricule(),
					'nom' => $candidat->getCandidat()->getNom(),
					'prenoms' => $candidat->getCandidat()->getPrenoms(),
					'date_naissance' => $candidat->getCandidat()->getDateNaissance(),
					'lieu_naissance' => $candidat->getCandidat()->getLieuNaissance(),
					'carte_scoute' => $candidat->getCandidat()->getCarteScoute(),
					'date_entree' => $candidat->getCandidat()->getDateEntree(),
					'niveau_etude' => $candidat->getCandidat()->getNiveauEtude(),
					'profession' => $candidat->getCandidat()->getProfession(),
					'residence' => $candidat->getCandidat()->getResidence(),
					'email' => $candidat->getCandidat()->getEmail(),
					'sexe' => $candidat->getCandidat()->getSexe(),
					'photo' => $candidat->getCandidat()->getPhoto(),
					'region' => $candidat->getCandidat()->getRegion()->getNom(),
					'montant' => $montant,
					'contact' => $candidat->getCandidat()->getContact(),
					'fonction' => $candidat->getCandidat()->getFonction(),
					'age' => $age
				];
				$montant_total = $montant_total + (int) $candidat->getActivite()->getMontant();
			}
			$result = [
				'participant' => $data,
				'total' => $montant_total
			];
			
			return $result;
		}
		
		/**
		 * Montant a payer
		 *
		 * @param $montant
		 * @return float|int
		 */
		public function montantAPayer($montant)
		{
			$am = (int) $montant/(1 - 0.035);
			$am = $this->arrondiSuperieur($am, 5);
			
			return $am;
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
		
		/**
		 * Numero de dossier du candidat
		 *
		 * @param $activite
		 * @param $nombre
		 * @return string
		 */
		public function code($activite, $nombre): string
		{
			if ($nombre < 10) $numero ='00'.$nombre;
			elseif ($nombre < 100) $numero = '0'.$nombre;
			else $numero = $nombre;
			
			return $code = $activite->getCode().'-'.$numero;
		}
		
	}
