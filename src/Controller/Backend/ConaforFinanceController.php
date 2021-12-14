<?php

namespace App\Controller\Backend;

use App\Entity\Activite;
use App\Utilities\GestionCandidature;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conafor/finance")
 */
class ConaforFinanceController extends AbstractController
{
	private $_candidature;
	
	public function __construct(GestionCandidature $_candidature)
	{
		$this->_candidature = $_candidature;
	}
	
    /**
     * @Route("/", name="conafor_finance_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
		$data = $this->_candidature->fincance(); //dd($data);
        return $this->render('conafor_finance/index.html.twig', [
            'participants' => $data['participant'],
            'montant' => $data['total'],
	        'activites' => $this->getDoctrine()->getRepository(Activite::class)->findBy([],['id'=>'DESC'])
        ]);
    }
}
