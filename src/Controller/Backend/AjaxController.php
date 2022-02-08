<?php
	
	namespace App\Controller\Backend;
	
	use App\Entity\Candidater;
	use App\Entity\Sygesca\Membre;
	use App\Utilities\GestionActivite;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Encoder\XmlEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use Symfony\Component\Serializer\Serializer;
	
	/**
	 * @Route("/ajax")
	 */
	class AjaxController extends AbstractController
	{
		private $_activite;
		
		public function __construct(GestionActivite $_activite)
		{
			$this->_activite = $_activite;
		}
		
		/**
		 * @Route("/{matricule}", name="requete_ajax_matricule", methods={"GET"})
		 */
		public function matricule ($matricule)
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);

			$scout = $this->getDoctrine()->getRepository(Membre::class, 'sygesca')->findOneBy(['matricule'=>$matricule, 'cotisation'=>$this->_activite->annee()]);
			
			return $this->json($scout);
			
		}
		
		/**
		 * @Route("/{matricule}/paiement", name="requete_ajax_paiment", methods={"GET"})
		 */
		public function paiement(Request $request, $matricule)
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);
			
			$id_transaction = time().''.substr(uniqid("",true), -9, 5); //dd($id_transaction);
			
			$candidater = $this->getDoctrine()->getRepository(Candidater::class)->findOneByMatricule($matricule);
			
			$data=[];
			if ($candidater){
				if (!$candidater->getIdTransactionSolde()) $idTransaction = $id_transaction;
				else $idTransaction = $candidater->getIdTransactionSolde();
				$data = [
					'nom' => $candidater->getCandidat()->getNom(),
					'prenoms' => $candidater->getCandidat()->getPrenoms(),
					'id_transaction' => $idTransaction,
					'montant' => $candidater->getMontant(),
					'matricule' => $candidater->getCandidat()->getMatricule(),
					'region' => $candidater->getCandidat()->getRegion()->getNom(),
					'code' => $candidater->getCode(),
					'description' => "test",
					'id' => $candidater->getId()
				];
			}
			
			return $this->json($data);
		}
	}
