<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sygesca")
 */
class SygescaDashboardController extends AbstractController
{
    /**
     * @Route("/", name="sygesca_dashboard")
     */
    public function index(): Response
    {
        return $this->render('sygesca_dashboard/index.html.twig', [
            'controller_name' => 'SygescaDashboardController',
        ]);
    }
}
