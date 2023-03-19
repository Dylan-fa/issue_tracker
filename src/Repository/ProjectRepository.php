<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
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
            SELECT p.id, p.name, p.description
            FROM project p
            WHERE
            MATCH (name, description) AGAINST (:query IN NATURAL LANGUAGE MODE)
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['query' => $query]);
        return $resultSet->fetchAllAssociative();
    }
}