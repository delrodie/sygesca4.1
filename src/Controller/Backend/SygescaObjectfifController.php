<?php

namespace App\Controller\Backend;

use App\Entity\Sygesca3\Objectif;
use App\Form\Sygesca3\ObjectifType;
use App\Utilities\GestionCotisation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sygesca/objectfif")
 */
class SygescaObjectfifController extends AbstractController
{
	private $_cotisation;
	
	public function __construct(GestionCotisation $_cotisation)
	{
		$this->_cotisation = $_cotisation;
	}
	
    /**
     * @Route("/", name="sygesca_objectfif_index", methods={"GET"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
		$annee = $this->_cotisation->annee();
        $objectifs = $entityManager
            ->getRepository(Objectif::class)
            ->findAll();
	
	
	    $objectif = new Objectif();
	    $form = $this->createForm(ObjectifType::class, $objectif);
	    $form->handleRequest($request);
	
	    if ($form->isSubmitted() && $form->isValid()) {
		    $entityManager->persist($objectif);
		    $entityManager->flush();
		
		    return $this->redirectToRoute('sygesca_objectfif_index', [], Response::HTTP_SEE_OTHER);
	    }

        return $this->renderForm('sygesca_objectfif/index.html.twig', [
            'objectifs' => $objectifs,
	        'annee' => $annee,
            'objectif' => $objectif,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="sygesca_objectfif_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $objectif = new Objectif();
        $form = $this->createForm(ObjectifType::class, $objectif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($objectif);
            $entityManager->flush();

            return $this->redirectToRoute('sygesca_objectfif_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sygesca_objectfif/new.html.twig', [
            'objectif' => $objectif,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sygesca_objectfif_show", methods={"GET"})
     */
    public function show(Objectif $objectif): Response
    {
        return $this->render('sygesca_objectfif/show.html.twig', [
            'objectif' => $objectif,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sygesca_objectfif_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Objectif $objectif, EntityManagerInterface $entityManager): Response
    {
		$annee = $this->_cotisation->annee();
        $form = $this->createForm(ObjectifType::class, $objectif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			$objectif->setAnnee($annee);
            $entityManager->flush();

            return $this->redirectToRoute('sygesca_objectfif_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sygesca_objectfif/edit.html.twig', [
            'objectif' => $objectif,
            'form' => $form,
	        'objectifs' => $entityManager->getRepository(Objectif::class)->findAll(),
	        'annee' => $annee
        ]);
    }

    /**
     * @Route("/{id}", name="sygesca_objectfif_delete", methods={"POST"})
     */
    public function delete(Request $request, Objectif $objectif, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$objectif->getId(), $request->request->get('_token'))) {
            $entityManager->remove($objectif);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sygesca_objectfif_index', [], Response::HTTP_SEE_OTHER);
    }
}
