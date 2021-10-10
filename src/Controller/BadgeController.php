<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/badge")
 */
class BadgeController extends AbstractController
{
    /**
     * @Route("/", name="badge_recherche")
     */
    public function index(): Response
    {
        return $this->render('badge/index.html.twig', [
            'controller_name' => 'BadgeController',
        ]);
    }
}
