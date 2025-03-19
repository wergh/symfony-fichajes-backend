<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Controller\User;

use App\Application\Mapper\User\UserListDtoMapper;
use App\Application\UseCase\User\GetUsersUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetUsersController extends AbstractController
{

    public function __construct(
        private readonly GetUsersUseCase $getUsersUseCase,
        private readonly UserListDtoMapper $userDTOMapper,
    ) {}

    #[Route('/users', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        try {
            $users = $this->getUsersUseCase->execute();
            $usersDTO = array_map(fn($user) => $this->userDTOMapper->toDTO($user), $users);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Users retrieved successfully', 'data' => $usersDTO], Response::HTTP_CREATED);

    }
}
