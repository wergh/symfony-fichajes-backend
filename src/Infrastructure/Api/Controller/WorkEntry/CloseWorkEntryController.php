<?php

declare(strict_types=1);


namespace App\Infrastructure\Api\Controller\WorkEntry;

use App\Application\Mapper\WorkEntry\WorkEntryDtoMapper;
use App\Application\UseCase\WorkEntry\CloseWorkEntryUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\WorkEntry\Exception\NotWorkEntryOpenException;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class CloseWorkEntryController extends AbstractController
{

    public function __construct(
        private readonly CloseWorkEntryUseCase $closeWorkEntryUseCase,
        private readonly WorkEntryDtoMapper    $workEntryDtoMapper
    )
    {
    }

    #[Route('/work-entry/close/{userId}', methods: ['GET'])]
    public function _invoke(string $userId): JsonResponse
    {
        try {
            $workEntry = $this->closeWorkEntryUseCase->execute($userId);
        } catch (EntityNotFoundException|NotWorkEntryOpenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $workEntryDto = $this->workEntryDtoMapper->toDTO($workEntry);
        return new JsonResponse(['message' => 'Work Entry closed successfully', 'data' => $workEntryDto], Response::HTTP_OK);
    }
}
