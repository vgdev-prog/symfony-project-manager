<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Type;

use App\Shared\Domain\ValueObject\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class EmailType extends StringType
{
    public const NAME = 'email';

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Email
    {
        if (!$value) return null;

        return Email::fromString($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) return null;

        if ($value instanceof Email) {
            return $value->getValue();
        }
        return $value;
    }

    public function getName():string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

}
