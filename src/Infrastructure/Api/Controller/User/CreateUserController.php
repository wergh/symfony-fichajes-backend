<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Controller\User;

use App\Application\Command\User\CreateUserCommand;
use App\Application\UseCase\User\CreateUserUseCase;
use App\Infrastructure\Api\Request\User\CreateUserRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class CreateUserController extends AbstractController
{

    public function __construct(
        private readonly CreateUserUseCase $createUserUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {}

    #[Route('/users', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $createUserRequest = $this->serializer->deserialize(
            $request->getContent(),
            CreateUserRequest::class,
            'json'
        );

        $errors = $this->validator->validate($createUserRequest);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $command = new CreateUserCommand($createUserRequest->getName());
            $this->createUserUseCase->execute($command);
        } catch (ValidationFailedException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }


        return new JsonResponse(['message' => 'User created successfully'], Response::HTTP_CREATED);
    }

}
