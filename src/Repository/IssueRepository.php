<?php

namespace App\Repository;

use App\Entity\Issue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Issue::class);

    }

    public function save(Issue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Issue $entity, bool $flush = false): void
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
            SELECT i.title, i.description, i.id, p.name AS pname, s.name AS sname, p.color AS pcolor, s.color AS scolor
            FROM issue i
            JOIN priority p ON p.id=i.priority_id
            JOIN status s ON s.id=i.status_id
            WHERE
            MATCH (title, description) AGAINST (:query IN NATURAL LANGUAGE MODE)
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['query' => $query]);
        return $resultSet->fetchAllAssociative();
    }

    public function findByAssignee($user)
    {
        return $this->createQueryBuilder('i')
            ->where('i.assignee = :user')
            ->setParameter('user', $user)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findRecentIssues()
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult()
        ;
    }
}