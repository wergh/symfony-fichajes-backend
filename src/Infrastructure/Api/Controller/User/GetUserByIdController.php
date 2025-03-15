<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Controller\User;

use App\Application\Mapper\User\UserDtoMapper;
use App\Application\UseCase\User\GetUserByIdUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetUserByIdController extends AbstractController
{

    public function __construct(
        private readonly GetUserByIdUseCase $getUserByIdUseCase,
        private readonly UserDtoMapper $userDTOMapper,
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('/user/{id}', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        try {
            $user = $this->getUserByIdUseCase->execute($id);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $userDTO = $this->userDTOMapper->toDTO($user);


        return new JsonResponse(
            $this->serializer->serialize($userDTO, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
