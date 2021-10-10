<?php

namespace App\Controller\Backend;

use App\Entity\Compte;
use App\Entity\User;
use App\Form\CompteType;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sygesca/admin/compte")
 */
class SygescaAdminCompteController extends AbstractController
{
    /**
     * @Route("/", name="sygesca_admin_compte_index", methods={"GET", "POST"})
     */
    public function index(CompteRepository $compteRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
		$compte = new Compte();
	    $form = $this->createForm(CompteType::class, $compte);
	    $form->handleRequest($request);
	
	    if ($form->isSubmitted() && $form->isValid()) {
			$user = $entityManager->getRepository(Compte::class)->findOneBy(['user'=>$compte->getUser()]);
			if ($user){
				$this->addFlash('danger', "Cet utilisateur a déjà été associé à une région");
				return $this->redirectToRoute('sygesca_admin_compte_index');
			}
		    $entityManager->persist($compte);
		    $entityManager->flush();
			
			$this->addFlash('success', "Compte enregistré avec succès");
		
		    return $this->redirectToRoute('sygesca_admin_compte_index', [], Response::HTTP_SEE_OTHER);
	    }
		
        return $this->renderForm('sygesca_admin_compte/index.html.twig', [
            'comptes' => $compteRepository->findAll(),
	        'compte' => $compte,
	        'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="sygesca_admin_compte_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($compte);
            $entityManager->flush();

            return $this->redirectToRoute('sygesca_admin_compte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sygesca_admin_compte/new.html.twig', [
            'compte' => $compte,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sygesca_admin_compte_show", methods={"GET"})
     */
    public function show(Compte $compte): Response
    {
        return $this->render('sygesca_admin_compte/show.html.twig', [
            'compte' => $compte,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sygesca_admin_compte_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Compte $compte, EntityManagerInterface $entityManager, CompteRepository $compteRepository): Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
	        $user = $entityManager->getRepository(Compte::class)->findOneBy(['user'=>$compte->getUser()]);
	        if ($user){
		        $this->addFlash('danger', "Cet utilisateur a déjà été associé à une région");
		        return $this->redirectToRoute('sygesca_admin_compte_index');
	        }
            $entityManager->flush();

            return $this->redirectToRoute('sygesca_admin_compte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sygesca_admin_compte/edit.html.twig', [
            'compte' => $compte,
            'form' => $form,
	        'comptes' => $compteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="sygesca_admin_compte_delete", methods={"POST"})
     */
    public function delete(Request $request, Compte $compte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compte->getId(), $request->request->get('_token'))) {
            $entityManager->remove($compte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sygesca_admin_compte_index', [], Response::HTTP_SEE_OTHER);
    }
}
