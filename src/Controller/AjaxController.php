<?php
	
	namespace App\Controller;
	
	use App\Entity\Sygesca3\District;
	use App\Entity\Sygesca3\Fonctions;
	use App\Entity\Sygesca3\Groupe;
	use App\Entity\Sygesca3\Region;
	use App\Entity\Sygesca3\Scout;
	use App\Utilities\GestionAdhesion;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Encoder\XmlEncoder;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use Symfony\Component\Serializer\Serializer;
	
	/**
	 * @Route("/ajax")
	 */
	class AjaxController extends AbstractController
	{
		private $_adhesion;
		
		public function __construct(GestionAdhesion $_adhesion)
		{
			$this->_adhesion = $_adhesion;
		}
		
		/**
		 * @Route("/{matricule}", name="requete_ajax_matricule", methods={"GET"})
		 */
		public function matricule ($matricule)
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);
			
			$scout = $this->getDoctrine()->getRepository(Scout::class, 'sygesca')->findOneBy(['matricule'=>$matricule]);
			
			return $this->json($scout);
			
		}
		
		/**
		 * @Route("/requete/form", name="requete_ajax_formulaire", methods={"GET","POST"})
		 */
		public function formulaire(Request $request)
		{
			//Initialisation
			$encoders = [new XmlEncoder(), new JsonEncoder()];
			$normalizers = [new ObjectNormalizer()];
			$serializer = new Serializer($normalizers, $encoders);
			
			$field = $request->get('field');
			$value = $request->get('value');
			$fonction_request = $request->get('fonction');
			
			if ($field === 'region'){//dd($fonction_request);
				if ($fonction_request === '23' || $fonction_request === '24' || $fonction_request === '25')
					$districts = $this->getDoctrine()->getRepository(District::class)->findByEquipeByRegion($value, 'EQUIPE REGIONALE');
				else
					$districts = $this->getDoctrine()->getRepository(District::class)->findBy(['region' => $value],['nom'=>"ASC"]);
				
				$data = $this->json($districts);
			}elseif ($field === 'district'){
				if ($fonction_request === '20' || $fonction_request === '21' || $fonction_request === '22')
					$groupes = $this->getDoctrine()->getRepository(Groupe::class)->findEquipeByDistrict($value, 'district');
				else
					$groupes = $this->getDoctrine()->getRepository(Groupe::class)->findBy(['district' => $value],['paroisse'=>"ASC"]);
				$data = $this->json($groupes);
			}elseif ($field === 'fonction'){
				$fonction = $this->getDoctrine()->getRepository(Fonctions::class)->findOneBy(['id' => $value])->getMontant();
				$result = (int)$fonction / (1 - 0.035); //dd($fonction);
				$result = $this->_adhesion->arrondiSuperieur($result, 5);
				$data = $this->json($result);
			}elseif ($field === 'regionIntialisation'){
				$regions = $this->getDoctrine()->getRepository(Region::class)->findAll();
				$data = $this->json($regions);
			}
			else{
				$data = $this->json([]);
			}
			//dd($data);
			return $data;
			
		}
	}
