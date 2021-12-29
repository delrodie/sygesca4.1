<?php

namespace App\Controller;

use App\Entity\Adherant;
use App\Entity\Validation;
use App\Utilities\GestionCotisation;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/validation/automatique", name="validation_automatique_")
 */
class ValidationAutomatiqueController extends AbstractController
{
	private $_cotisation;
	private $_session;
	private $_em;
	
	public function __construct(GestionCotisation $_cotisation, SessionInterface $_session, EntityManagerInterface $_em)
	{
		$this->_cotisation = $_cotisation;
		$this->_session = $_session;
		$this->_em = $_em;
	}
	
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
		$validation = $this->apiCinetpay();
		
        return $this->render('validation_automatique/index.html.twig', [
            'validation' => $validation,
        ]);
    }
	
	function apiCinetpay()
	{
		// Verification de l'existence de la table validation
		$validation = $this->getDoctrine()->getRepository(Validation::class)->findOneBy([],['id'=>"DESC"]);
		
		if (!$validation || $validation->getIdTransaction() === '1634416788.23234545') {
			$validation = new Validation();
			$adherants = $this->getDoctrine()->getRepository(Adherant::class)->findListNonValid();
		}else { //dd($session);
			$adherants = $this->getDoctrine()->getRepository(Adherant::class)->findListNonValidProcessus($validation->getIdTransaction());
		}
		
		foreach ($adherants as $adherant){
				//dd($adherant);
			$validation->setIdTransaction($adherant->getIdtransaction());
			$validation->setAdherant($adherant->getId());
			
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
			]; //dd($validation);
			$context = stream_context_create($options); //dd($context);
			$result =  file_get_contents('https://api-checkout.cinetpay.com/v2/payment/check', false, $context);
			$donnee = json_decode($result);
			if ($donnee->code === '00'){
				$query[$i++] = file_get_contents('http://adhesion.scoutascci.org/cinetpay/notify?cpm_trans_id='.$adherant->getIdTransaction(),false);
				$validation->setNombre((int)$validation->getNombre()+1);
			}
			
			if (!$validation->getId()) $this->_em->persist($validation);
			
			$this->_em->flush();
		}
		
		return $validation;
	}
}
