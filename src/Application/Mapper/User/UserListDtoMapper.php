<?php

declare(strict_types=1);

namespace App\Application\Mapper\User;

use App\Application\Dto\User\UserListDto;
use App\Domain\User\Entity\User;

class UserListDtoMapper
{

    public function toDTO(User $user): UserListDto
    {
        return new UserListDto(
            (string) $user->getId(),
            $user->getName(),
        );
    }
}
