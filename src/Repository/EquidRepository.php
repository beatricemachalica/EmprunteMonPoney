<?php

namespace App\Repository;

use App\Entity\Equid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Equid|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equid|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equid[]    findAll()
 * @method Equid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquidRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equid::class);
    }

    // /**
    //  * @return Equid[] Returns an array of Equid objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Equid
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
