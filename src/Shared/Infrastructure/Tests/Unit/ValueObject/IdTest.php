<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Tests\Unit\ValueObject;

use App\Shared\Domain\Exception\InvalidUuidException;
use App\Shared\Domain\ValueObject\Id;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class IdTest extends TestCase
{
    public function test_is_id_next_generate_valid_value()
    {
        $id = Id::next();

        $this->assertTrue(Uuid::isValid($id->getValue()));
    }

    public function test_is_get_value_return_valid_value()
    {
        $uuid = 'a0ad4155-8fef-434c-9900-00d4f99193f1';
        $id = Id::fromString($uuid);

        $this->assertTrue(Uuid::isValid($id->getValue()));
        $this->assertEquals($uuid, $id->getValue());
    }

    public function test_is_generate_from_string_break_with_exception()
    {
        $uuid = 'hello';

        $this->expectException(InvalidUuidException::class);
        $this->expectExceptionMessage(sprintf('"%s" is not a valid UUID.', $uuid));

        Id::fromString($uuid);
    }

    public function test_is_uuid_equals_return_true()
    {
        $uuid1 = Id::fromString('a0ad4155-8fef-434c-9900-00d4f99193f1');
        $uuid2 = Id::fromString('a0ad4155-8fef-434c-9900-00d4f99193f1');

        $this->assertTrue($uuid1->equals($uuid2));
    }

    public function test_is_uuid_equals_return_false()
    {
        $one = Id::fromString('2a69ba8c-97b7-48a1-b7e7-99c69e1cf896');
        $two = Id::fromString('a0ad4155-8fef-434c-9900-00d4f99193f1');

        $this->assertFalse($one->equals($two));
    }

    public function test_is_to_string_working()
    {
        $uuid = '2a69ba8c-97b7-48a1-b7e7-99c69e1cf896';
        $fromString = (string) Id::fromString($uuid);

        $this->assertEquals($uuid, $fromString);
    }

}
