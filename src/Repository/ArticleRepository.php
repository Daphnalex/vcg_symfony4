<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getArticleById($id): ?Article
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.id = ' .$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAllArticlesSortedByDescCreatedAt()
    {
        $qb = $this->createQueryBuilder('article')
            ->orderBy('article.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function getLastArticlesSortedByDescCreatedAt()
    {
        $qb = $this->createQueryBuilder('article')
            ->orderBy('article.createdAt', 'DESC')
            ->setMaxResults(5);

        return $qb->getQuery()->getResult();
    }
}
