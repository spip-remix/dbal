<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use SpipRemix\Component\Dbal\Exception\AbstractDbalException;
use SpipRemix\Component\Dbal\Exception\SchemaException;
use SpipRemix\Component\Dbal\Schema;
use SpipRemix\Component\Dbal\TableInterface;

#[CoversClass(Schema::class)]
#[CoversClass(AbstractDbalException::class)]
class SchemaTest extends TestCase
{
    public function testInstantiation(): void
    {
        // Given
        $arraySchema = $this->getSchema();

        // When
        $actual = new Schema($arraySchema['name'], $arraySchema['prefix']);

        // Then
        $this->assertEquals('test', $actual->getName());
        $this->assertEquals('test', $actual->getPrefix());
        $this->assertEmpty($actual->getTables());
    }

    public function testCannotInstantiateWithoutName(): void
    {
        // Given
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Un schéma doit avoir un nom valide. "" donné');

        // When
        new Schema('');

        // Then
        // Throws an exception
    }

    public function testAddTable(): void
    {
        // Given
        $arraySchema = $this->getSchema();
        $actual = new Schema($arraySchema['name']);

        // When
        $actual->addTable($this->getTable());

        // Then
        $this->assertCount(1, $actual->getTables());
    }

    public function testCannotAddTableTwice(): void
    {
        $this->expectExceptionMessage('Une table portant le nom "stub" existe déjà.');

        // Given
        $arraySchema = $this->getSchema();
        $actual = new Schema($arraySchema['name']);

        // When
        $actual->addTable($this->getTable());
        $actual->addTable($this->getTable());

        // Then
        // Throws an exception
    }

    public static function dataGetTable(): array
    {
        return [
            'exists' => [
                'expected' => true,
                'tableName' => 'stub'
            ],
            'not-exists' => [
                'expected' => \false,
                'tableName' => 'not_exists'
            ],
        ];
    }

    #[DataProvider('dataGetTable')]
    public function testGetTable($expected, $tableName): void
    {
        // Given
        $arraySchema = $this->getSchema();
        $schema = new Schema($arraySchema['name']);
        $schema->addTable($this->getTable());

        // When
        $table = $schema->getTable($tableName);

        // Then
        $actual = \in_array(
            TableInterface::class,
            \class_implements($table ?? \stdClass::class)
        );
        $this->assertSame($expected, $actual);
    }
}
