<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Controller\User;

use App\Application\Command\User\CreateUserCommand;
use App\Application\Mapper\User\UserDtoMapper;
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
        private readonly UserDtoMapper $userDtoMapper,
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
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $command = new CreateUserCommand($createUserRequest->getName());
            $user = $this->createUserUseCase->execute($command);
        } catch (ValidationFailedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $userDTO = $this->userDtoMapper->toDTO($user);

        return new JsonResponse(['message' => 'User created successfully', 'data' => $userDTO], Response::HTTP_CREATED);
    }

}
