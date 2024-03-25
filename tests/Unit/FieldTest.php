<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use SpipRemix\Component\Dbal\Exception\AbstractDbalException;
use SpipRemix\Component\Dbal\Exception\FieldException;
use SpipRemix\Component\Dbal\Field;
use SpipRemix\Component\Dbal\TableInterface;
use SpipRemix\Component\Dbal\Test\Fixtures\StubTable;

#[CoversClass(Field::class)]
#[CoversClass(AbstractDbalException::class)]
class FieldTest extends TestCase
{
    public function testInstantiation(): void
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

    public function testInstantiationByExplode(): void
    {
        // Given
        $dataType = 'text DEFAULT \'\'';

        // When
        $actual = new Field('test', $dataType);

        // Then
        $this->assertEquals("''", $actual->getDefault());
    }

    public function testCannotInstantiateWithoutName(): void
    {
        // Given
        $this->expectExceptionMessage('Un champ doit avoir un nom et un dataType valide. "" et "" donnés');

        // When
        new Field('', '');

        // Then
        // Throws an exception
    }

    public function testCannotInstantiateWithInvalidDataType(): void
    {
        // Given
        $this->expectException(FieldException::class);
        $this->expectExceptionMessage('Un champ doit avoir un nom et un dataType valide. "test" et "DEFAULT \'\' NOT NULL" donnés');

        // When
        new Field('test', 'DEFAULT \'\' NOT NULL');

        // Then
        // Throws an exception
    }
}
