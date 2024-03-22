<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use SpipRemix\Component\Dbal\FieldInterface;
use SpipRemix\Component\Dbal\Field;
use SpipRemix\Component\Dbal\TableInterface;
use SpipRemix\Component\Dbal\Test\Fixtures\StubField;
use SpipRemix\Component\Dbal\Test\Fixtures\StubSchema;
use SpipRemix\Component\Dbal\Test\Fixtures\StubTable;

#[CoversClass(Field::class)]
class FieldTest extends TestCase
{
    public function testInstanciation(): void
    {
        // Given
        $arraySchema = $this->getSchema();
        $table = new StubTable();

        // When
        $actual = new Field(
            $arraySchema['tables'][0]['fields'][0]['name'],
            $arraySchema['tables'][0]['fields'][0]['dataType']
        );
        $actual->setTable($table);

        // Then
        $this->assertEquals('test', $actual->getName());
        $this->assertEquals('VARCHAR(255)', $actual->getDataType());
        $this->assertNull($actual->getDefault());
        $this->assertFalse($actual->getNullable());
        $this->assertEquals('stub.test', $actual->getFullName());
        $this->assertEquals('stub.test', $actual->getFullFullName());
        $this->assertInstanceOf(TableInterface::class, $actual->getTable());
    }

    public function testCannotInstantiateWithoutName(): void
    {
        // Given
        $this->expectExceptionMessage('Un champ doit avoir un nom et un dataType.');

        // When
        new Field('', '');

        // Then
        // Throws an exception
    }
}
