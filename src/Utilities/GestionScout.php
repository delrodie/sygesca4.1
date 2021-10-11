<?php
	
	namespace App\Utilities;
	
	use App\Entity\Cotisation;
	use App\Entity\Membre;
	use App\Entity\Sygesca3\Fonctions;
	use App\Entity\Sygesca3\Region;
	use App\Entity\Sygesca3\Scout;
	use Doctrine\ORM\EntityManagerInterface;
	
	class GestionScout
	{
		private $em;
		
		public function __construct(EntityManagerInterface $em)
		{
			$this->em = $em;
		}
		
		public function getListByBranche($annee, $statut, $branche, $region=null,$district=null,$groupe=null): array
		{
			$cotisations = $this->em->getRepository(Cotisation::class)->findByBranche($annee, $statut, $branche, $region,$district,$groupe);
			$lists = []; $i=0;
			foreach ($cotisations as $cotisation){
				$lists[$i++]=[
					'loop_index' => $i,
					'region' => $cotisation->getMembre()->getGroupe()->getDistrict()->getRegion()->getNom(),
					'region_slug' => $cotisation->getMembre()->getGroupe()->getDistrict()->getRegion()->getSlug(),
					'district' => $cotisation->getMembre()->getGroupe()->getDistrict()->getNom(),
					'district_slug' => $cotisation->getMembre()->getGroupe()->getDistrict()->getSlug(),
					'groupe' => $cotisation->getMembre()->getGroupe()->getParoisse(),
					'groupe_id' => $cotisation->getMembre()->getGroupe()->getId(),
					'statut' => $cotisation->getMembre()->getStatut()->getLibelle(),
					'matricule' => $cotisation->getMembre()->getMatricule(),
					'carte' => $cotisation->getMembre()->getCarte(),
					'identite_civile' => strtoupper($cotisation->getMembre()->getNom()).' '.ucwords($cotisation->getMembre()->getPrenoms()),
					'nom' => $cotisation->getMembre()->getNom(),
					'prenoms' => $cotisation->getMembre()->getPrenoms(),
					'date_naissance' => $cotisation->getMembre()->getDateNaissance(),
					'lieu_naissance' => $cotisation->getMembre()->getLieuNaissance(),
					'sexe' => $cotisation->getMembre()->getSexe(),
					'contact' => $cotisation->getMembre()->getContact(),
					'urgence' => $cotisation->getMembre()->getUrgence(),
					'contact_urgence' => $cotisation->getMembre()->getContactUrgence(),
					'fonction' => $cotisation->getMembre()->getFonction(),
					'montant' => $cotisation->getMontant(),
					'montant_sans_frais' => $cotisation->getMontantSansFrais(),
					'ristourne' => $cotisation->getRistourne(),
					'telephone' => $cotisation->getTelephone(),
					'created_at' => $cotisation->getCreatedAt()
				];
			}
			
			return $lists;
		}
		
		public function branche($annee, $statut, $branche=null, $region=null,$district=null,$groupe=null): array
		{
			$branche = [
				'louveteau' => count($this->getListByBranche($annee,$statut,'LOUVETEAU',$region,$district,$groupe)),
				'eclaireur' => count($this->getListByBranche($annee,$statut,'ECLAIREUR',$region,$district,$groupe)),
				'cheminot' => count($this->getListByBranche($annee,$statut,'CHEMINOT',$region,$district,$groupe)),
				'routier' => count($this->getListByBranche($annee,$statut,'ROUTIER',$region,$district,$groupe)),
			];
			return $branche;
		}
		
		/**
		 * Liste des membres pour l'année
		 *
		 * @param null $region
		 * @param null $district
		 * @param null $groupe
		 * @return array
		 */
		public function getListScout($annee, $region=null,$district=null,$groupe=null): array
		{
			$cotisations = $this->em->getRepository(Cotisation::class)->findList($annee, $region,$district,$groupe);
			if (!$cotisations){
				$lists = [];
			}else{
				$lists = []; $i=0;
				foreach ($cotisations as $cotisation){
					$lists[$i++]=[
						'loop_index' => $i,
						'region' => $cotisation->getMembre()->getGroupe()->getDistrict()->getRegion()->getNom(),
						'region_slug' => $cotisation->getMembre()->getGroupe()->getDistrict()->getRegion()->getSlug(),
						'district' => $cotisation->getMembre()->getGroupe()->getDistrict()->getNom(),
						'district_slug' => $cotisation->getMembre()->getGroupe()->getDistrict()->getSlug(),
						'groupe' => $cotisation->getMembre()->getGroupe()->getParoisse(),
						'groupe_id' => $cotisation->getMembre()->getGroupe()->getId(),
						'statut' => $cotisation->getMembre()->getStatut()->getLibelle(),
						'matricule' => $cotisation->getMembre()->getMatricule(),
						'carte' => $cotisation->getMembre()->getCarte(),
						'identite_civile' => strtoupper($cotisation->getMembre()->getNom()).' '.ucwords($cotisation->getMembre()->getPrenoms()),
						'nom' => $cotisation->getMembre()->getNom(),
						'prenoms' => $cotisation->getMembre()->getPrenoms(),
						'date_naissance' => $cotisation->getMembre()->getDateNaissance(),
						'lieu_naissance' => $cotisation->getMembre()->getLieuNaissance(),
						'sexe' => $cotisation->getMembre()->getSexe(),
						'contact' => $cotisation->getMembre()->getContact(),
						'urgence' => $cotisation->getMembre()->getUrgence(),
						'contact_urgence' => $cotisation->getMembre()->getContactUrgence(),
						'fonction' => $cotisation->getMembre()->getFonction(),
						'montant' => $cotisation->getMontant(),
						'montant_sans_frais' => $cotisation->getMontantSansFrais(),
						'ristourne' => $cotisation->getRistourne(),
						'telephone' => $cotisation->getTelephone(),
						'created_at' => $cotisation->getCreatedAt()
					];
				}
			}
			
			return $lists;
		}
		
		/**
		 * @param $annee
		 * @param null $region
		 * @param null $district
		 * @param null $groupe
		 * @return array|int[]
		 */
		public function getFinance($annee, $region=null, $district=null, $groupe=null): array
		{
			$cotisations = $this->em->getRepository(Cotisation::class)->findList($annee, $region,$district,$groupe);
			
			$montant=0; $frais=0; $ristourne=0;
			
			foreach ($cotisations as $cotisation){
				$montant = $montant + (int) $cotisation->getMontantSansFrais();
				$ristourne = $ristourne + (int) $cotisation->getRistourne();
				$frais = $frais + ((int) $cotisation->getMontant() - (int) $cotisation->getMontantSansFrais());
			}
			
			$lists = [
				'montant' => $montant,
				'ristourne' => $ristourne,
				'frais' => $frais
			];
			
			return $lists;
		}
		
		
		public function getFonctionByAge($date)
		{
			$age = date('Y') - date('Y', strtotime($date)); //dd($age);
			
			return $this->em->getRepository(Fonctions::class)->findByAge($age);
		}
		
		/**
		 * Generation du matricule
		 *
		 * @param $region
		 * @return string
		 */
		public function matricule($region): string
		{
			$region_code = $this->em->getRepository(Region::class)->findOneBy(['id'=>$region])->getCode();
			
			// Verification de non existence
			$stop = true;
			while ($stop){
				$matricule = $region_code.''.$this->code_aleatoire().''.$this->lettre_aleatoire();
				$exist = $this->em->getRepository(Membre::class)->findOneBy(['matricule'=>$matricule]);
				$exist2 = $this->em->getRepository(Scout::class, 'sygesca')->findOneBy(['matricule'=>$matricule]);
				if ($exist) $stop = true;
				elseif ($exist2) $stop = true;
				else $stop = false;
			}
			
			return $matricule;
		}
		
		/**
		 * @param object $scout
		 * @param string $region_code
		 * @param int $id
		 * @return bool
		 */
		public function carte(object $scout, string $region_code, int $id): bool
		{
			$mois_encours = Date('m', time());
			if ($mois_encours > 9){
				$an = Date('y', time())+1;
			}else{
				$an = Date('y', time());
			}
			
			if ($id < 10){
				$num = '0000'.$id;
			}elseif($id < 100){
				$num = '000'.$id;
			}elseif ($id < 1000){
				$num = '00'.$id;
			}elseif ($id < 10000){
				$num = '0'.$id;
			}else{
				$num = $id;
			}
			
			$carte = $region_code.''.$an.'-'.$num;
			$scout->setCarte($carte); //dd($scout);
			$this->em->flush();
			
			return true;
		}
		
		/**
		 * Generation du code aleatoire pour constituer le matricule du scout
		 *
		 * @return int
		 */
		private function code_aleatoire():int
		{
			return mt_rand(10000,99999);
		}
		
		/**
		 * Generation du lettre aleatoire pour constituer le matricule du scout
		 * @return string
		 */
		private function lettre_aleatoire():string
		{
			// Liste des lettres de l'alphabet
			$alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			
			return $alphabet[rand(0,25)];
		}
		
	}
