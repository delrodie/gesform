<?php

namespace App\Repository;

use App\Entity\Candidater;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Candidater|null find($id, $lockMode = null, $lockVersion = null)
 * @method Candidater|null findOneBy(array $criteria, array $orderBy = null)
 * @method Candidater[]    findAll()
 * @method Candidater[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidaterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidater::class);
    }
	
	/**
	 * Liste des candidatures
	 *
	 * @param null $validation
	 * @param null $activite
	 * @return int|mixed|string
	 */
	public function findByValidationAndActiviteOrNo($validation=null, $activite=null)
	{ //dd($validation);
		$query = $this->createQueryBuilder('c')
			->addSelect('ca')
			->addSelect('a')
			->addSelect('r')
			->leftJoin('c.candidat', 'ca')
			->leftJoin('c.activite', 'a')
			->leftJoin('ca.region', 'r')
			;
		if ($activite){
			$query->where('a.id = :activite')
				->andWhere('c.validation = :validation')
				->setParameters([
					'activite'=> $activite,
					'validation' => $validation
				]);
		}elseif ($validation){
			$query->where('c.validation = :validation')
				->setParameter('validation', $validation);
		}
		return $query->getQuery()->getResult();
	}
	
	/**
	 * @param $candidater
	 * @return int|mixed|string|null
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findOneById($candidater)
	{
		return $this
			->createQueryBuilder('c')
			->addSelect('a')
			->addSelect('ca')
			->addSelect('r')
			->leftJoin('c.activite', 'a')
			->leftJoin('c.candidat', 'ca')
			->leftJoin('ca.region', 'r')
			->where('c.id = :candidater')
			->setParameter('candidater', $candidater)
			->setMaxResults(1)
			->getQuery()->getOneOrNullResult()
			;
	}
	
	/**
	 * @param $matricule
	 * @return int|mixed|string|null
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findOneByMatricule($matricule)
	{
		return $this
			->createQueryBuilder('c')
			->addSelect('a')
			->addSelect('ca')
			->addSelect('r')
			->leftJoin('c.activite', 'a')
			->leftJoin('c.candidat', 'ca')
			->leftJoin('ca.region', 'r')
			->where('ca.matricule = :matricule')
			->andWhere('c.validation = :validation')
			->setParameters([
				'matricule' => $matricule,
				'validation' => 1
			])
			->setMaxResults(1)
			->getQuery()->getOneOrNullResult()
			;
	}
	
	public function findParticipantByActiviteOrNo($activite=null)
	{
		$query = $this->createQueryBuilder('c')
			->addSelect('ca')
			->addSelect('a')
			->addSelect('r')
			->leftJoin('c.candidat', 'ca')
			->leftJoin('c.activite', 'a')
			->leftJoin('ca.region', 'r')
		;
		if ($activite){
			$query->where('a.id = :activite')
				->andWhere('c.statusPaiement = :status')
				->setParameters([
					'activite'=> $activite,
					'status' => 'VALIDE'
				]);
		}else{
			$query->where('c.statusPaiement = :status')
				->setParameter('status', 'VALIDE');
		}
		return $query->getQuery()->getResult();
	}

    // /**
    //  * @return Candidater[] Returns an array of Candidater objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Candidater
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
