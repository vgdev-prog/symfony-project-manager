<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use App\User\Domain\Enum\UserRole;

final class Role
{
    public function __construct(
        private string $name
    )
    {
        if (!in_array($name, [UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], true)) {

        }
    }


}
