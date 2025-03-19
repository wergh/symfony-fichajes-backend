<?php

declare(strict_types=1);


namespace App\Application\UseCase\WorkEntry;

use App\Application\Command\WorkEntry\CreateWorkEntryCommand;
use App\Application\UseCase\User\GetUserByIdUseCase;
use App\Application\Validator\WorkEntry\CreateWorkEntryValidatorInterface;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\Shared\ValueObject\IdGeneratorInterface;
use App\Domain\User\Exception\WorkEntryAlreadyOpenException;
use App\Domain\User\Service\UserDomainService;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryWriteRepositoryInterface;
use App\Domain\WorkEntry\ValueObject\WorkEntryId;
use DateTimeImmutable;

final class CreateWorkEntryUseCase
{
    public function __construct(
        private WorkEntryWriteRepositoryInterface $workEntryWriteRepository,
        private CreateWorkEntryValidatorInterface $validator,
        private GetUserByIdUseCase                $getUserByIdUseCase,
        private IdGeneratorInterface              $idGenerator,
        private UserDomainService                 $userDomainService,
    )
    {
    }

    /**
     * @throws WorkEntryAlreadyOpenException
     * @throws EntityNotFoundException
     */
    public function execute(CreateWorkEntryCommand $command): WorkEntry
    {
        $this->validator->validate($command);
        $user = $this->getUserByIdUseCase->execute($command->getUserId());

        $this->userDomainService->validateUserCanCreateWorkEntry($user);

        $workEntry = new WorkEntry(
            id: WorkEntryId::create($this->idGenerator),
            user: $user,
            startDate: new DateTimeImmutable()
        );

        $this->workEntryWriteRepository->save($workEntry);
        return $workEntry;
    }
}
