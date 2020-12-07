<?php

namespace App\Repository;

use App\Entity\TypesContracts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypesContracts|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypesContracts|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypesContracts[]    findAll()
 * @method TypesContracts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypesContractsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypesContracts::class);
    }

    // /**
    //  * @return TypesContracts[] Returns an array of TypesContracts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypesContracts
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
