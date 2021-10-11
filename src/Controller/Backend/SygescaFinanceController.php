<?php

namespace App\Controller\Backend;

use App\Entity\Sygesca3\Region;
use App\Repository\RegionRepository;
use App\Utilities\GestionCotisation;
use App\Utilities\GestionScout;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sygesca/finance")
 */
class SygescaFinanceController extends AbstractController
{
	private $_scout;
	private $_cotisation;
	private $regionRepository;
	
	public function __construct(GestionScout $_scout, GestionCotisation $_cotisation, RegionRepository $regionRepository)
	{
		$this->_scout = $_scout;
		$this->_cotisation = $_cotisation;
		$this->regionRepository = $regionRepository;
	}
	
    /**
     * @Route("/", name="sygesca_finance_liste", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
	    // Declaration variable
	    $annee = $this->_cotisation->annee();
	    $region = null;
	
	    // Request
	    $regionID = $request->get('region');
	
	    if ($regionID){
		    $region = $this->regionRepository->findOneBy(['id'=>$regionID]);
		    $finance = $this->_scout->getFinance($annee, $regionID);
		    $template = 'sygesca_finance/region.html.twig';
	    }else{
		    $finance = $this->_scout->getFinance($annee);
		    $template = 'sygesca_finance/index.html.twig';
	    }
	
	    return $this->render($template, [
		    'regions' => $this->getDoctrine()->getRepository(Region::class)->findAll(),
		    'region' => $region,
		    'finance' => $finance
	    ]);
    }
}
