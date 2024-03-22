<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit\Converter;

use PHPUnit\Framework\Attributes\CoversClass;
use SpipRemix\Component\Dbal\Converter\ArrayConverter;
use SpipRemix\Component\Dbal\Test\Fixtures\StubFactory;
use SpipRemix\Component\Dbal\Test\Unit\TestCase;

#[CoversClass(ArrayConverter::class)]
class ArrayConverterTest extends TestCase
{
    public function testConvert(): void
    {
        // Given
        $arraySchema = $this->getSchema();
        $converter = new ArrayConverter(new StubFactory, $arraySchema);

        // When
        $actual = $converter->convert();

        // Then
        $this->assertEquals('stub', $actual->getName());
        $this->assertCount(1, $actual->getTables());
        $this->assertEquals('stub', $actual->getTables()['stub']->getName());
        $this->assertCount(1, $actual->getTables()['stub']->getFields());
        $this->assertEquals('stub', $actual->getTables()['stub']->getFields()['stub']->getName());
    }
}