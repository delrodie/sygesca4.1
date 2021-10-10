<?php
	
	namespace App\Utilities;
	
	use App\Entity\Sygesca3\Fonctions;
	use Doctrine\ORM\EntityManagerInterface;
	
	class GestionScout
	{
		private $em;
		
		public function __construct(EntityManagerInterface $em)
		{
			$this->em = $em;
		}
		
		public function getFonctionByAge($date)
		{
			$age = date('Y') - date('Y', strtotime($date)); //dd($age);
			
			return $this->em->getRepository(Fonctions::class)->findByAge($age);
		}
	}
