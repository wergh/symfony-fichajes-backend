<?php

declare(strict_types=1);


namespace App\Infrastructure\Api\Controller\WorkEntry;

use App\Application\Command\WorkEntry\UpdateWorkEntryCommand;
use App\Application\UseCase\WorkEntry\UpdateWorkEntryUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\User\Exception\EndDateInTheFutureNotAllowed;
use App\Domain\WorkEntry\Exception\NotOverlapException;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use App\Domain\WorkEntry\Exception\WorkEntryIsAlreadyOpenException;
use App\Infrastructure\Api\Request\WorkEntry\UpdateWorkEntryRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class UpdateWorkEntryController extends AbstractController
{
    public function __construct(
        private readonly UpdateWorkEntryUseCase $updateWorkEntryUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    )
    {
    }

    #[Route('/work-entry/{workEntryId}', methods: ['PATCH'])]
    public function _invoke(string $workEntryId, Request $request): JsonResponse
    {

        $updateWorkEntryRequest = $this->serializer->deserialize(
            $request->getContent(),
            UpdateWorkEntryRequest::class,
            'json'
        );

        $errors = $this->validator->validate($updateWorkEntryRequest);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $command = new UpdateWorkEntryCommand(
                $updateWorkEntryRequest->getUserId(),
                $workEntryId,
                $updateWorkEntryRequest->getStartDateAsDateTimeImmutable(),
                $updateWorkEntryRequest->getEndDateAsDateTimeImmutable()
            );

            $this->updateWorkEntryUseCase->execute($command);
        } catch (ValidationFailedException|WorkEntryIsAlreadyOpenException|NotOverlapException|EndDateInTheFutureNotAllowed $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedAccessToWorkEntry $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse(['message' => 'Work entry updated successfully'], Response::HTTP_OK);
    }
}
