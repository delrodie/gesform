<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Candidat;
use App\Entity\Candidater;
use App\Entity\Formation;
use App\Utilities\GestionActivite;
use App\Utilities\GestionCandidature;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/valdiation")
 */
class ValdiationController extends AbstractController
{
	private $_candidature;
	private $session;
	private $_activite;
	
	public function __construct(GestionCandidature $_candidature, SessionInterface $session, GestionActivite $_activite)
	{
		$this->_candidature = $_candidature;
		$this->session = $session;
		$this->_activite = $_activite;
	}
	
	/**
	 * @Route("/", name="app_validation_index", methods={"GET","POST"})
	 */
	public function index()
	{
		$candidat = $this->session->get('candidat');
		$this->session->clear();
		return $this->render('validation/success.html.twig',[
			'candidat' => $candidat
		]);
	}
	
    /**
     * @Route("/{slug}", name="app_valdiation", methods={"GET","POST"})
     */
    public function validation(Request $request, $slug): Response
    {
		$candidat = $this->getDoctrine()->getRepository(Candidat::class)->findOneBy(['slug'=>$slug]);
		$date = date('Y-m-d', time());
		
		// Verification de la candidature
	    $verification = $this->_candidature->verifCandidature($candidat, $date); //dd($verification);
		if ($verification){ //dd($verification);
			$this->session->set('candidat', $candidat);
			return $this->redirectToRoute('app_validation_index');
		}
		
		if ($request->get('scout_slug') === $slug){
			$candidater = $this->_candidature->validation($request, $candidat);
			
			$this->addFlash('success', "Votre candidature a bien été prise en compte. Vous serez notifié(e) par mail dès validation du CONAFOR.");
			$this->session->set('candidat', $candidat);
			return $this->redirectToRoute('app_validation_acompte', ['id' => $candidater->getId()]);
		}
		//dd($this->_activite->findActivte());
        return $this->render('valdiation/index.html.twig', [
            'candidat' => $candidat,
	        'formations' => $this->getDoctrine()->getRepository(Formation::class)->findBy(['candidat'=>$candidat->getId()],['date'=>"DESC"]),
	        'activite' => $this->_activite->findActivte()
        ]);
    }
	
	/**
	 * @Route("/acompte/{id}", name="app_validation_acompte", methods={"GET"})
	 */
	public function acompte(Request $request, Candidater $candidater)
	{
		$parametres = [
			"amount" => $candidater->getAcompte(),
            "currency"=> "XOF",
            "apikey"=> '18714242495c8ba3f4cf6068.77597603',
            'site_id' => 356950,
            "transaction_id" => $candidater->getIdTransaction(),
            "description" => "Acompte ".$candidater->getCandidat()->getMatricule(),
            "return_url" => $this->generateUrl('app_validation_index',[], UrlGeneratorInterface::ABSOLUTE_URL),
            "notify_url" => $this->generateUrl('cinetpay_notify_acompte',[],UrlGeneratorInterface::ABSOLUTE_URL),
            "customer_id"=> $candidater->getCandidat()->getMatricule(),
            "customer_name" => $candidater->getCandidat()->getNom(),
            "customer_surname" => $candidater->getCandidat()->getPrenoms(),
            "customer_city" => $candidater->getCandidat()->getRegion()->getNom(),
			"channels" => "MOBILE_MONEY",
			"invoice_data" => [
				"Reste à payer" => $candidater->getMontant(),
				"Matricule" => $candidater->getCandidat()->getMatricule(),
				'Formation' => $candidater->getActivite()->getNom()
			]
		];
		
		$options = [
			'http' => [
				'method' =>"POST",
				'header' => "Content-Type: application/json\r\n",
				//'ignore_errors' => true,
				'content' => json_encode($parametres)
			]
		]; dd($options);
		$context = stream_context_create($options); //dd($context);
		$result =  file_get_contents('https://api-checkout.cinetpay.com/v2/payment', false, $context);
		$donnee = json_decode($result);
		
		dd($donnee);
		
		
		if ($donnee->code === '201'){ //dd($donnee);
			$data = [
				'response_id' => $donnee->api_response_id,
                'token' => $donnee->data->payment_token,
                'url' => $donnee->data->payment_url,
                'candidate' => $candidater->getId()
			]; //dd($data);
			
			$this->_candidature->cinetpay_acompte($data);
		}
		
		return $this->redirectToRoute('app_validation_index');
	}
}
