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
    public function getPaginatedPost($page, $limit, $categoriesFilter = null, $minPriceFilter = null, $maxPriceFilter = null)
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.active = 1');

        // categories filter
        if ($categoriesFilter != null) {
            $query->andWhere('p.category IN(:categories)')
                ->setParameter(':categories', array_values($categoriesFilter));
        }

        // minimum price filter
        if ($minPriceFilter != null) {
            $query->andWhere('p.price >= (:minPrice)')
                ->setParameter(':minPrice', $minPriceFilter);
        }

        // maximum price filter
        if ($maxPriceFilter != null) {
            $query->andWhere('p.price <= (:maxPrice)')
                ->setParameter(':maxPrice', $maxPriceFilter);
        }

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
    public function getAmountPosts($categoriesFilter = null, $minPriceFilter = null, $maxPriceFilter = null)
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.active = 1');

        // categories filter
        if ($categoriesFilter != null) {
            $query->andWhere('p.category IN(:categories)')
                ->setParameter(':categories', array_values($categoriesFilter));
        }

        // minimum price filter
        if ($minPriceFilter != null) {
            $query->andWhere('p.price >= (:minPrice)')
                ->setParameter(':minPrice', $minPriceFilter);
        }

        // maximum price filter
        if ($maxPriceFilter != null) {
            $query->andWhere('p.price <= (:maxPrice)')
                ->setParameter(':maxPrice', $maxPriceFilter);
        }

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
