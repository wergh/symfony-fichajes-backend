<?php

declare(strict_types=1);


namespace App\Application\UseCase\WorkEntry;


use App\Application\UseCase\User\GetUserByIdUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use Doctrine\Common\Collections\Collection;

final readonly class GetUserWorkEntriesUseCase
{
    public function __construct(
        private GetUserByIdUseCase $getUserByIdUseCase
    )
    {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function execute(string $id): Collection
    {
        $user = $this->getUserByIdUseCase->execute($id);
        return $user->getWorkEntries();

    }
}
