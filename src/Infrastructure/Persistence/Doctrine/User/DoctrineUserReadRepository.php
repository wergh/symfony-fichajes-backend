<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\User;


use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserReadRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class DoctrineUserReadRepository implements UserReadRepositoryInterface
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function all(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function findById(string $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }


}
