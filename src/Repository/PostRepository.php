<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Returns all posts per page
     * @return void 
     */
    public function getPaginatedPost($page, $limit, $filters = null)
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.active = 1');

        // On filtre les données
        // if ($filters != null) {
        //     $query->andWhere('p.category IN(:cats)')
        //         ->setParameter(':cats', array_values($filters));
        // }

        $query->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page * $limit) - $limit)
            // set nb of first item => $page(1) * $limit(10) = 10 - $limit(10) = 0 (nb of the first item of page 1)
            ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }

    /**
     * Returns number of posts
     * @return void 
     */
    public function getAmountPosts($filters = null)
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.active = 1');

        // On filtre les données
        // if ($filters != null) {
        //     $query->andWhere('a.categories IN(:cats)')
        //         ->setParameter(':cats', array_values($filters));
        // }

        // getSingleScalarResult() in order to avoid an array as a result
        return $query->getQuery()->getSingleScalarResult();
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
