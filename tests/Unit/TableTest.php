<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use SpipRemix\Component\Dbal\Exception\AbstractDbalException;
use SpipRemix\Component\Dbal\Exception\TableException;
use SpipRemix\Component\Dbal\FieldInterface;
use SpipRemix\Component\Dbal\Table;
use SpipRemix\Component\Dbal\Test\Fixtures\StubField;
use SpipRemix\Component\Dbal\Test\Fixtures\StubSchema;

#[CoversClass(Table::class)]
#[CoversClass(AbstractDbalException::class)]
class TableTest extends TestCase
{
    public function testInstantiation(): void
    {
        // Given
        $arraySchema = $this->getSchema();
        $schema = new StubSchema($arraySchema['name']);

        // When
        $actual = new Table($arraySchema['tables'][0]['name']);
        $schema->addTable($actual);

        // Then
        $this->assertEquals('test', $actual->getName());
        $this->assertEquals('test', $actual->getPrefixedName());
        $this->assertEquals('stub.test', $actual->getFullName());
        $this->assertEmpty($actual->getFields());
    }

    public function testCannotInstantiateWithoutName(): void
    {
        // Given
        $this->expectException(TableException::class);
        $this->expectExceptionMessage('Une table doit avoir un nom valide. "" donné');

        // When
        new Table('');

        // Then
        // Throws an exception
    }

    public function testGetField(): void
    {
        // Given
        $table = (new Table('test'))->addField(new StubField());

        // When
        $actual1 = $table->getField('stub');
        $actual2 = $table->getField('not_exist');

        // Then
        $this->assertInstanceOf(FieldInterface::class, $actual1);
        $this->assertNull($actual2);
    }

    public function testCannotAddFieldTwice(): void
    {
        $this->expectExceptionMessage('Un champ portant le nom "stub" existe déjà.');

        // Given
        $table = (new Table('test'))->addField(new StubField());

        // When
        $table->addField(new StubField());

        // Then
        // Throws an exception
    }
}
