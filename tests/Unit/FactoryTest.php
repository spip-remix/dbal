<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit\Converter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use SpipRemix\Component\Dbal\Exception\AbstractDbalException;
use SpipRemix\Component\Dbal\Exception\FieldException;
use SpipRemix\Component\Dbal\Factory;
use SpipRemix\Component\Dbal\Field;
use SpipRemix\Component\Dbal\Schema;
use SpipRemix\Component\Dbal\Table;
use SpipRemix\Component\Dbal\Test\Unit\TestCase;

#[CoversClass(Factory::class)]
#[CoversClass(Schema::class)]
#[CoversClass(Table::class)]
#[CoversClass(Field::class)]
#[CoversClass(AbstractDbalException::class)]
class FactoryTest extends TestCase
{
    public function testCreateSchema(): void
    {
        // Given
        $factory = new Factory();

        // When
        $actual = $factory->createSchema(name:'test', prefix:'test');

        // Then
        $this->assertEquals('test', $actual->getName());
    }

    public function testCreateTable(): void
    {
        // Given
        $factory = new Factory();

        // When
        $actual = $factory->createTable(name:'test');

        // Then
        $this->assertEquals('test', $actual->getName());
    }

    public function testCreateField(): void
    {
        // Given
        $factory = new Factory();

        // When
        $actual = $factory->createField(name:'test', dataType:'text');

        // Then
        $this->assertEquals('test', $actual->getName());
    }

    public function testCanNotCreateSchema(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Paramètres invalides.');

        // Given
        $factory = new Factory();

        // When
        $factory->createSchema();

        // Then
        // Throws an exception
    }

    public function testCanNotCreateTable(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Paramètres invalides.');

        // Given
        $factory = new Factory();

        // When
        $factory->createTable(...['name' => '']);

        // Then
        // Throws an exception
    }

    public function testCanNotCreateField(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Paramètres invalides.');

        // Given
        $factory = new Factory();

        // When
        $factory->createField(...['name' => 'test']);

        // Then
        // Throws a PHP UnexpectedValueException
    }

    public static function dataCanNotCreateFieldWithNonStringValues(): array
    {
        return [
            'empty-string' => [
                'expected' => '',
                'name' => '',
            ],
            'bool' => [
                'expected' => 'true',
                'name' => true,
            ],
            'array' => [
                'expected' => 'test',
                'name' => ['test'],
            ],
        ];
    }

    #[DataProvider('dataCanNotCreateFieldWithNonStringValues')]
    public function testCanNotCreateFieldWithNonStringValues($expected, $name): void
    {
        $this->expectException(FieldException::class);
        $this->expectExceptionMessage('Un champ doit avoir un nom et un dataType valide. "'.$expected.'" et "TEST" donnés');

        // Given
        $factory = new Factory();

        // When
        $factory->createField(...['name' => $name, 'dataType' => 'TEST']);

        // Then
        // Throws a FieldException
    }
}
