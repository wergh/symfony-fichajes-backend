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
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('/users', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $users = $this->getUsersUseCase->execute();
        $usersDTO = array_map(fn($user) => $this->userDTOMapper->toDTO($user), $users);

        return new JsonResponse(
            $this->serializer->serialize($usersDTO, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
