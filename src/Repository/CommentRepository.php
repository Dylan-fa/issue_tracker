<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByTextQuery($query)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT c.id, c.issue_id, c.text, c.created_at, u.username
            FROM comment c
            JOIN user u ON u.id=c.author_id
            WHERE
            MATCH (text) AGAINST (:query IN NATURAL LANGUAGE MODE)
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['query' => $query]);
        return $resultSet->fetchAllAssociative();
    }

    public function findRecentComments()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult()
        ;
    }
}