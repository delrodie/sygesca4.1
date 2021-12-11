<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sygesca/adherant")
 */
class SygescaAdherantController extends AbstractController
{
    /**
     * @Route("/", name="sygesca_adherant_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        return $this->render('sygesca_adherant/index.html.twig', [
            'controller_name' => 'SygescaAdherantController',
        ]);
    }
}
