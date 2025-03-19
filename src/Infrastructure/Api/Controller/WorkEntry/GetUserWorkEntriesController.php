<?php

declare(strict_types=1);


namespace App\Infrastructure\Api\Controller\WorkEntry;

use App\Application\Mapper\WorkEntry\WorkEntryDtoMapper;
use App\Application\UseCase\WorkEntry\GetUserWorkEntriesUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetUserWorkEntriesController extends AbstractController
{

    public function __construct(
        private readonly GetUserWorkEntriesUseCase $getUserWorkEntriesUseCase,
        private readonly WorkEntryDtoMapper $workEntryDtoMapper,
    )
    {
    }

    /**
     * @param string $userId
     * @return JsonResponse
     */
    #[Route('/work-entries/{userId}', methods: ['GET'])]
    public function __invoke(string $userId): JsonResponse
    {
        try {
            //En un caso real utilizaríamos el Token para obtener el userId no lo pasaríamos como argumento
            $workEntries = $this->getUserWorkEntriesUseCase->execute($userId);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $workEntriesDTO = array_map(fn($workEntry) => $this->workEntryDtoMapper->toDTO($workEntry), $workEntries->toArray());

        return new JsonResponse(
            ['message' => 'WorkEntries retrieved successfully', 'data' => $workEntriesDTO],
            Response::HTTP_OK,
        );

    }
}
