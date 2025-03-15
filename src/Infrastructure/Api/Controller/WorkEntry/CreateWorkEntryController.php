<?php

declare(strict_types=1);


namespace App\Infrastructure\Api\Controller\WorkEntry;

use App\Application\Command\WorkEntry\CreateWorkEntryCommand;
use App\Application\UseCase\WorkEntry\CreateWorkEntryUseCase;
use App\Domain\User\Exception\WorkEntryAlreadyOpenException;
use App\Infrastructure\Api\Request\WorkEntry\CreateWorkEntryRequest;
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
class CreateWorkEntryController extends AbstractController
{

    public function __construct(
        private readonly CreateWorkEntryUseCase $createWorkEntryUseCase,
        private readonly SerializerInterface    $serializer,
        private readonly ValidatorInterface     $validator,
    )
    {
    }

    #[Route('/work-entry', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $createWorkEntryRequest = $this->serializer->deserialize(
            $request->getContent(),
            CreateWorkEntryRequest::class,
            'json'
        );

        $errors = $this->validator->validate($createWorkEntryRequest);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $command = new CreateWorkEntryCommand($createWorkEntryRequest->getUserId());
            $this->createWorkEntryUseCase->execute($command);
        } catch (ValidationFailedException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (WorkEntryAlreadyOpenException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return new JsonResponse(['message' => 'Work entry created successfully'], Response::HTTP_CREATED);

    }
}
