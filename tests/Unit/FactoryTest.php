<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit\Converter;

use PHPUnit\Framework\Attributes\CoversClass;
use SpipRemix\Component\Dbal\Factory;
use SpipRemix\Component\Dbal\Field;
use SpipRemix\Component\Dbal\Schema;
use SpipRemix\Component\Dbal\Table;
use SpipRemix\Component\Dbal\Test\Unit\TestCase;

#[CoversClass(Factory::class)]
#[CoversClass(Schema::class)]
#[CoversClass(Table::class)]
#[CoversClass(Field::class)]
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
        $this->expectExceptionMessage('Un schéma doit avoir un nom et un préfixe.');

        // Given
        $factory = new Factory();

        // When
        $factory->createSchema();

        // Then
        // Throws an exception
    }

    public function testCanNotCreateTable(): void
    {
        $this->expectExceptionMessage('Une table doit avoir un nom.');

        // Given
        $factory = new Factory();

        // When
        $factory->createTable();

        // Then
        // Throws an exception
    }

    public function testCanNotCreateField(): void
    {
        $this->expectExceptionMessage('Un champ doit avoir et nom et un dataType valide.');

        // Given
        $factory = new Factory();

        // When
        $factory->createField();

        // Then
        // Throws an exception
    }
}
