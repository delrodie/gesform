<?php

namespace App\Controller;

use App\Utilities\GestionActivite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
	private $_activite;
	
	public function __construct(GestionActivite $_activite)
	{
		$this->_activite = $_activite;
	}
	
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'activite' => $this->_activite->findActiviteEncours(),
        ]);
    }
}
