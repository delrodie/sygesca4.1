<?php
	
	namespace App\Repository;
	
	use App\Entity\Cotisation;
	use Doctrine\Persistence\ManagerRegistry;
	
	/**
	 * @method Cotisation|null find($id, $lockMode = null, $lockVersion = null)
	 * @method Cotisation|null findOneBy(array $criteria, array $orderBy = null)
	 * @method Cotisation[]    findAll()
	 * @method Cotisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
	 */
	class CotisationRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, Cotisation::class);
		}
	}
