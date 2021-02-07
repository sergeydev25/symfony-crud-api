<?php

declare(strict_types=1);

namespace App\Model\Classroom\Repository;

use App\Model\Classroom\Entity\Classroom;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

class ClassroomRepository
{
    private EntityManagerInterface $entityManager;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Classroom::class);
    }

    public function all(): array
    {
        return $this->repository->findAll();
    }

    public function findAllPaginate(bool $isActive, bool $isAll = true): QueryBuilder
    {
        $query = $this->repository->createQueryBuilder('t')->orderBy('t.createdAt', 'desc');
        if (false === $isAll) {
            $query->andWhere('t.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        return $query;
    }

    public function add(Classroom $photo): void
    {
        $this->entityManager->persist($photo);
    }

    public function remove(Classroom $photo): void
    {
        $this->entityManager->remove($photo);
    }
}
