<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Type;

use App\Shared\Domain\ValueObject\Id;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use InvalidArgumentException;

final class IdType extends StringType
{
    public const NAME = 'uuid';

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Id
    {
        if (!$value) return null;

        return Id::fromString($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) return null;

        if ($value instanceof Id) {
            return $value->getValue();
        }
        if (is_string($value)) {
            return $value;
        }

        throw new InvalidArgumentException(
            sprintf('Expected Id or string, got %s', get_debug_type($value))
        );

    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

}
