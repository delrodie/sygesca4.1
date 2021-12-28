<?php

namespace App\Controller;

use App\Entity\Adherant;
use App\Utilities\GestionCotisation;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/validation/automatique", name="validation_automatique_")
 */
class ValidationAutomatiqueController extends AbstractController
{
	private $_cotisation;
	
	public function __construct(GestionCotisation $_cotisation)
	{
		$this->_cotisation = $_cotisation;
	}
	
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
		$annee = $this->_cotisation->annee();
		$adherants = $this->getDoctrine()->getRepository(Adherant::class)->findListNonValid();
		$query=[];$i=0;
		foreach ($adherants as $adherant){
			$parametres = [
				'apikey' => '18714242495c8ba3f4cf6068.77597603',
				'site_id' => 422630,
				'token' => $adherant->getToken()
			];
			$options = [
				'http' => [
					'method' =>"POST",
					'header' => "Content-Type: application/json\r\n",
					//'ignore_errors' => true,
					'content' => json_encode($parametres)
				]
			];
			$context = stream_context_create($options); //dd($context);
			$result =  file_get_contents('https://api-checkout.cinetpay.com/v2/payment/check', false, $context);
			$donnee = json_decode($result);
			if ($donnee->code === '00'){
				$query[$i++] = file_get_contents('http://adhesion.scoutascci.org/cinetpay/notify?cpm_trans_id='.$adherant->getIdTransaction(),false);
			}
		}
        return $this->render('validation_automatique/index.html.twig', [
            'queries' => $query,
        ]);
    }
}
