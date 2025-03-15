<?php

declare(strict_types=1);


namespace App\Infrastructure\Persistence\Doctrine\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserWriteRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class DoctrineUserWriteRepository implements UserWriteRepositoryInterface
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }


    public function save(User $user, $flush = true): void
    {
        $this->entityManager->persist($user);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
