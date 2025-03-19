<?php

declare(strict_types=1);


namespace App\Infrastructure\Api\Controller\WorkEntry;

use App\Application\UseCase\WorkEntry\DeleteWorkEntryUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class DeleteWorkEntryController extends AbstractController
{

    public function __construct(
        private readonly DeleteWorkEntryUseCase $deleteWorkEntryUseCase,
    )
    {
    }

    #[Route('/work-entry/delete/{userId}/{workEntryId}', methods: ['DELETE'])]
    public function _invoke($userId, $workEntryId): JsonResponse
    {
        try {
            $this->deleteWorkEntryUseCase->execute($userId, $workEntryId);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedAccessToWorkEntry $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['message' => 'Work Entry deleted successfully'], Response::HTTP_OK);
    }
}
