<?php
	
	namespace App\Controller;
	
	use App\Entity\Activite;
	use App\Entity\Candidater;
	use App\Utilities\GestionCandidature;
	use App\Utilities\GestionMail;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Encoder\XmlEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use Symfony\Component\Serializer\Serializer;
	
	/**
	 * @Route("/cinetpay")
	 */
	class CinetpayController extends AbstractController
	{
		private $_candidature;
		private $_em;
		private $_mail;
		
		public function __construct(GestionCandidature $_candidature, EntityManagerInterface $_em, GestionMail $_mail)
		{
			$this->_candidature = $_candidature;
			$this->_em = $_em;
			$this->_mail = $_mail;
		}
		
		/**
		 * @Route("/", name="cinetpay_paiement", methods={"GET","POST"})
		 */
		public function paiement(Request $request)
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);
			
			$data = [
				'token' => $request->get('token'),
				'candidate' => $request->get('candidater'),
				'response_id' => $request->get('api_response_id'),
				'url' => $request->get('url')
			];
			
			$result = $this->_candidature->cinetpay($data);
			
			
			return $this->json($result);
		
		}
		
		/**
		 * @Route("/notify", name="cinetpay_notify", methods={"GET","POST"})
		 */
		public function notify(Request $request)
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);
			
			$cpmTransId = $request->get('cpm_trans_id');
			
			if (isset($cpmTransId)){
				try {
					$url = 'https://api-checkout.cinetpay.com/v2/payment/check';
					$apiKey = '18714242495c8ba3f4cf6068.77597603';
					$site_id = 356950;
					$plateform = "PROD"; // Valorisé à PROD si vous êtes en production
					
					// Verification du statut de la candidature
					//$adherant = $this->em->getRepository(Adherant::class)->findOneBy(['idtransaction'=>$cpmTransId]); //dd($adherant);
					$candidater = $this->_em->getRepository(Candidater::class)->findOneBy(['idTransaction'=>$cpmTransId]);
					if ($candidater){
						if ($candidater->getStatusPaiement() === 'VALIDE'){
							$data = [
								'status' => false,
								'matricule' => $candidater->getCandidat()->getMatricule()
							];
						}else{
							$data = [
								'apikey' => $apiKey,
								'site_id' => $site_id,
								'token' => $candidater->getToken()
							];
							
							// Creation d'option
							$options = [
								'http' => [
									'method' =>"POST",
									'header' => "Content-Type: application/json\r\n",
									//'ignore_errors' => true,
									'content' => json_encode($data)
								]
							]; //dd($options);
							
							// Creation du context
							$context = stream_context_create($options); //dd($context);
							
							// Execution de la requete
							$result =  file_get_contents('https://api-checkout.cinetpay.com/v2/payment/check', false, $context);
							
							$donnee = json_decode($result); //dd($donnee);
							if ($donnee->code === '00'){
								
								// Generation du code de dossier puis mise a jour de la table candidater
								$this->mise_a_jour($candidater);
								
								$this->_mail->paiement($candidater);
							}
						}
					}
					
				} catch (\Exception $e){
					echo "Erreur :". $e->getMessage();
					$this->addFlash('danger', "Erreur : ".$e->getMessage());
				}
			}
			
			return $this->json($data);
			
		}
		
		/**
		 * @Route("/acompte/notify", name="cinetpay_notify_acompte", methods={"GET","POST"})
		 */
		public function notify_acompte(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);
			
			$cpmTransId = $request->get('cpm_trans_id');
			
			if (isset($cpmTransId)){
				try {
					$url = 'https://api-checkout.cinetpay.com/v2/payment/check';
					$apiKey = '18714242495c8ba3f4cf6068.77597603';
					$site_id = 356950;
					$plateform = "PROD"; // Valorisé à PROD si vous êtes en production
					
					// Verification du statut de la candidature
					//$adherant = $this->em->getRepository(Adherant::class)->findOneBy(['idtransaction'=>$cpmTransId]); //dd($adherant);
					$candidater = $this->_em->getRepository(Candidater::class)->findOneBy(['idTransaction'=>$cpmTransId]);
					//dd($cpmTransId);
					if ($candidater){
						if ($candidater->getStatusPaiement() === 'ACOMPTE'){
							$data = [
								'status' => false,
								'matricule' => $candidater->getCandidat()->getMatricule()
							];
						}else{
							$data = [
								'apikey' => $apiKey,
								'site_id' => $site_id,
								'token' => $candidater->getTokenAcompte()
							];
							
							// Creation d'option
							$options = [
								'http' => [
									'method' =>"POST",
									'header' => "Content-Type: application/json\r\n",
									//'ignore_errors' => true,
									'content' => json_encode($data)
								]
							]; //dd($options);
							
							// Creation du context
							$context = stream_context_create($options); //dd($context);
							
							// Execution de la requete
							$result =  file_get_contents('https://api-checkout.cinetpay.com/v2/payment/check', false, $context);
							
							$donnee = json_decode($result); //dd($donnee);
							if ($donnee->code === '00'){
								
								// Generation du code de dossier puis mise a jour de la table candidater
								$candidater->setStatusPaiement('ACOMPTE');
								$this->_em->flush();
								
								$this->_mail->acompte($candidater);
							}
						}
					}
					
				} catch (\Exception $e){
					echo "Erreur :". $e->getMessage();
					$this->addFlash('danger', "Erreur : ".$e->getMessage());
				}
			}
			
			return $this->json($data);
		}
		
		/**
		 * @param $candidter
		 * @return bool
		 */
		protected function mise_a_jour($candidter): bool
		{
			$date = date('Y-m-d', time());
			$activite = $this->_em->getRepository(Activite::class)->findEncours($date);
			
			// Gestion du numero de dossier
			$nombre_candidat = count($this->_em->getRepository(Candidater::class)->findBy(['activite'=>$activite->getId(), 'statusPaiement'=>'VALIDE']));
			$code = $this->_candidature->code($activite,$nombre_candidat+1);
			
			$candidter->setStatusPaiement('VALIDE');
			$candidter->setCode($code);
			
			$this->_em->flush();
			
			return true;
		}
		
		
	}
