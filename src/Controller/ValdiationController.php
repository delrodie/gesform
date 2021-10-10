<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Candidat;
use App\Entity\Formation;
use App\Utilities\GestionCandidature;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/valdiation")
 */
class ValdiationController extends AbstractController
{
	private $_candidature;
	private $session;
	
	public function __construct(GestionCandidature $_candidature, SessionInterface $session)
	{
		$this->_candidature = $_candidature;
		$this->session = $session;
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
			$this->_candidature->validation($request, $candidat);
			
			$this->addFlash('success', "Votre candidature a bien été prise en compte. Vous serez notifié(e) par mail dès validation du CONAFOR.");
			$this->session->set('candidat', $candidat);
			return $this->redirectToRoute('app_validation_index');
		}
		
        return $this->render('valdiation/index.html.twig', [
            'candidat' => $candidat,
	        'formations' => $this->getDoctrine()->getRepository(Formation::class)->findBy(['candidat'=>$candidat->getId()],['date'=>"DESC"]),
	        'activite' => $this->getDoctrine()->getRepository(Activite::class)->findEncours($date)
        ]);
    }
}
