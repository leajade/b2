<?php

namespace App\Repository;

use App\Entity\KindsContracts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method KindsContracts|null find($id, $lockMode = null, $lockVersion = null)
 * @method KindsContracts|null findOneBy(array $criteria, array $orderBy = null)
 * @method KindsContracts[]    findAll()
 * @method KindsContracts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KindsContractsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KindsContracts::class);
    }

    // /**
    //  * @return KindsContracts[] Returns an array of KindsContracts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?KindsContracts
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
