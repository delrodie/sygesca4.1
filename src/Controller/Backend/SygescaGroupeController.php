<?php

namespace App\Controller\Backend;

use App\Entity\Sygesca3\Groupe;
use App\Form\Sygesca3\GroupeType;
use App\Repository\GroupeRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sygesca/groupe")
 */
class SygescaGroupeController extends AbstractController
{
    /**
     * @Route("/", name="sygesca_groupe_index", methods={"GET","POST"})
     */
    public function index(GroupeRepository $groupeRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
	    $groupe = new Groupe();
	    $form = $this->createForm(GroupeType::class, $groupe);
	    $form->handleRequest($request);
	
	    if ($form->isSubmitted() && $form->isValid()) {
		    $slugify = new Slugify();
		    $slug = $slugify->slugify($groupe->getParoisse());
		    $groupe->setSlug($slug);
			
		    $entityManager->persist($groupe);
		    $entityManager->flush();
		
		    return $this->redirectToRoute('sygesca_groupe_index', [], Response::HTTP_SEE_OTHER);
	    }
		
        return $this->renderForm('sygesca_groupe/index.html.twig', [
            'groupes' => $groupeRepository->findList(),
	        'groupe' => $groupe,
	        'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="sygesca_groupe_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupe = new Groupe();
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($groupe);
            $entityManager->flush();

            return $this->redirectToRoute('sygesca_groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sygesca_groupe/new.html.twig', [
            'groupe' => $groupe,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sygesca_groupe_show", methods={"GET"})
     */
    public function show(Groupe $groupe): Response
    {
        return $this->render('sygesca_groupe/show.html.twig', [
            'groupe' => $groupe,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sygesca_groupe_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Groupe $groupe, EntityManagerInterface $entityManager, GroupeRepository $groupeRepository): Response
    {
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
	        $slugify = new Slugify();
	        $slug = $slugify->slugify($groupe->getParoisse());
	        $groupe->setSlug($slug);
			
            $entityManager->flush();

            return $this->redirectToRoute('sygesca_groupe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sygesca_groupe/edit.html.twig', [
            'groupe' => $groupe,
            'form' => $form,
	        'groupes' => $groupeRepository->findList(),
        ]);
    }

    /**
     * @Route("/{id}", name="sygesca_groupe_delete", methods={"POST"})
     */
    public function delete(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($groupe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sygesca_groupe_index', [], Response::HTTP_SEE_OTHER);
    }
}
