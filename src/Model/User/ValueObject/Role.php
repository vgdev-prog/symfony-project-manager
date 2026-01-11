<?php

declare(strict_types=1);

namespace App\Model\User\ValueObject;

use App\Model\User\Enum\UserRole;

readonly class Role
{
    public function __construct(
        private string $name
    )
    {
        if (!in_array($name, [UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], true)) {

        }
    }


}
