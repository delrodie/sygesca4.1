<?php

namespace App\Controller\Backend;

use App\Entity\Sygesca3\District;
use App\Form\Sygesca3\DistrictType;
use App\Repository\DistrictRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sygesca/district")
 */
class SygescaDistrictController extends AbstractController
{
    /**
     * @Route("/", name="sygesca_district_index", methods={"GET", "POST"})
     */
    public function index(DistrictRepository $districtRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
	    $district = new District();
	    $form = $this->createForm(DistrictType::class, $district);
	    $form->handleRequest($request);
	
	    if ($form->isSubmitted() && $form->isValid()) {
			$slugify = new Slugify();
			$slug = $slugify->slugify($district->getNom());
			$district->setSlug($slug);
		    $entityManager->persist($district);
		    $entityManager->flush();
		
		    return $this->redirectToRoute('sygesca_district_index', [], Response::HTTP_SEE_OTHER);
	    }
		
        return $this->renderForm('sygesca_district/index.html.twig', [
            'districts' => $districtRepository->findList(),
	        'district' => $district,
	        'form' => $form,
        ]);
    }

    /**
     * @Route("/new", name="sygesca_district_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $district = new District();
        $form = $this->createForm(DistrictType::class, $district);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($district);
            $entityManager->flush();

            return $this->redirectToRoute('sygesca_district_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sygesca_district/new.html.twig', [
            'district' => $district,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="sygesca_district_show", methods={"GET"})
     */
    public function show(District $district): Response
    {
        return $this->render('sygesca_district/show.html.twig', [
            'district' => $district,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sygesca_district_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, District $district,DistrictRepository $districtRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DistrictType::class, $district);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
	        $slugify = new Slugify();
	        $slug = $slugify->slugify($district->getNom());
	        $district->setSlug($slug);
			
            $entityManager->flush();

            return $this->redirectToRoute('sygesca_district_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sygesca_district/edit.html.twig', [
            'district' => $district,
            'form' => $form,
	        'districts' => $districtRepository->findList(),
        ]);
    }

    /**
     * @Route("/{id}", name="sygesca_district_delete", methods={"POST"})
     */
    public function delete(Request $request, District $district, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$district->getId(), $request->request->get('_token'))) {
            $entityManager->remove($district);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sygesca_district_index', [], Response::HTTP_SEE_OTHER);
    }
}
