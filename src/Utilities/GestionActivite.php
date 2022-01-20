<?php
	
	namespace App\Utilities;
	
	use App\Entity\Activite;
	use Doctrine\ORM\EntityManagerInterface;
	
	class GestionActivite
	{
		private $_em;
		
		public function __construct(EntityManagerInterface $_em)
		{
			$this->_em = $_em;
		}
		
		public function findActiviteEncours()
		{
			$today = date('Y-m-d', time());
			$activite = $this->_em->getRepository(Activite::class)->findEncours($today); //dd($activite);
			
			return $activite;
		}
		
		/**
		 * @return array
		 */
		public function findActivte()
		{
			$activite = [
				'id' => $this->findActiviteEncours()->getId(),
				'nom' => $this->findActiviteEncours()->getNom(),
				'code' => $this->findActiviteEncours()->getCode(),
				'montant' => $this->findActiviteEncours()->getMontant(),
				'slug' => $this->findActiviteEncours()->getSlug(),
				'description' => $this->findActiviteEncours()->getDescription(),
				'acompte' => (int) $this->findActiviteEncours()->getMontant() / 2
			];
			return $activite;
		}
		
		/**
		 * @return string
		 */
		public function annee(): string
		{
			$mois_encours = Date('m', time());
			if ($mois_encours > 9){
				$debut_annee = Date('Y', time());
				$fin_annee = Date('Y', time())+1;
				//$an = Date('y', time())+1;
			}else{
				$debut_annee = Date('Y', time())-1;
				$fin_annee = Date('Y', time());
				//$an = Date('y', time());
			}
			
			$annee = $debut_annee.'-'.$fin_annee;
			
			return $annee;
		}
	}
