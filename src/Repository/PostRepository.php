<?php

namespace App\Repository;

use App\Entity\Post;
use App\Data\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use ContainerWTfi0Qm\PaginatorInterface_82dac15;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
    }

    /**
     * Returns all posts per page
     * @return PaginationInterface
     */
    public function findSearch(SearchData $search): PaginationInterface
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p', 'a')
            ->join('p.category', 'c')
            ->leftJoin('p.activities', 'a');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.text LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->min)) {
            $query = $query
                ->andWhere('p.price >= :minPrice')
                ->setParameter(':minPrice', $search->min);
        }

        if (!empty($search->max)) {
            $query = $query
                ->andWhere('p.price <= :maxPrice')
                ->setParameter(':maxPrice', $search->max);
        }

        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN(:categories)')
                ->setParameter(':categories', $search->categories);
        }

        if (!empty($search->activities)) {
            $query = $query
                ->andWhere('a.id IN(:activities)')
                ->setParameter(':activities', $search->activities);
        }

        $query = $query->getQuery();

        return $this->paginator->paginate(
            $query,
            $search->page,
            21
        );
    }
}
