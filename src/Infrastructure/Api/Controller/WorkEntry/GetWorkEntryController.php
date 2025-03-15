<?php

declare(strict_types=1);


namespace App\Infrastructure\Api\Controller\WorkEntry;

use App\Application\Mapper\WorkEntry\WorkEntryDtoMapper;
use App\Application\UseCase\WorkEntry\GetWorkEntryByIdUseCase;
use App\Domain\Shared\Exceptions\EntityNotFoundException;
use App\Domain\WorkEntry\Exception\UnauthorizedAccessToWorkEntry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[AsController]
class GetWorkEntryController extends AbstractController
{
    public function __construct(
        private readonly GetWorkEntryByIdUseCase $getWorkEntryByIdUseCase,
        private readonly SerializerInterface $serializer,
        private readonly WorkEntryDtoMapper $workEntryDtoMapper
    )
    {
    }

    #[Route('/work-entry/{userId}/{workEntryId}', methods: ['GET'])]
    public function _invoke(string $userId, string $workEntryId): JsonResponse
    {
        try {
            //En un caso real utilizaríamos el Token para obtener el userId no lo pasaríamos como argumento
            $workEntry = $this->getWorkEntryByIdUseCase->execute($userId, $workEntryId);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedAccessToWorkEntry $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        $workEntryDto = $this->workEntryDtoMapper->toDTO($workEntry);

        return new JsonResponse(
            $this->serializer->serialize($workEntryDto, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

}
