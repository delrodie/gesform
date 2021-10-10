<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conafor")
 */
class ConaforDashboardController extends AbstractController
{
    /**
     * @Route("/", name="conafor_dashboard")
     */
    public function index(): Response
    {
        return $this->render('conafor_dashboard/index.html.twig', [
            'controller_name' => 'ConaforDashboardController',
        ]);
    }
}
