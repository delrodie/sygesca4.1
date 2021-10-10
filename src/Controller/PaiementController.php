<?php
	
	namespace App\Controller;
	
	use App\Utilities\GestionAdhesion;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Encoder\XmlEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use Symfony\Component\Serializer\Serializer;
	
	/**
	 * @Route("/paiement")
	 */
	class PaiementController extends AbstractController
	{
		private $_adhesion;
		
		public function __construct(GestionAdhesion $_adhesion)
		{
			$this->_adhesion = $_adhesion;
		}
		
		/**
		 * @Route("/", name="paiement_ancien", methods={"GET","POST"})
		 */
		public function ancien(Request $request)
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);
			
			$data = $this->_adhesion->formulaire($request);
			return $this->json($data);
		
		}
	}
