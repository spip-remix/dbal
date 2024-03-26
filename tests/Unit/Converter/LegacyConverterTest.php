<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit\Converter;

use PHPUnit\Framework\Attributes\CoversClass;
use SpipRemix\Component\Dbal\Converter\ArrayConverter;
use SpipRemix\Component\Dbal\Converter\LegacyConverter;
use SpipRemix\Component\Dbal\Schema;
use SpipRemix\Component\Dbal\Test\Fixtures\StubFactory;
use SpipRemix\Component\Dbal\Test\Unit\TestCase;

#[CoversClass(LegacyConverter::class)]
#[CoversClass(ArrayConverter::class)]
#[CoversClass(Schema::class)]
class LegacyConverterTest extends TestCase
{
    public function testConvert(): void
    {
        // Given
        $converter = new LegacyConverter(new StubFactory, [
            'stub'=> [
                'field' => [
                    'stub' => 'TEXT DEFAULT \'\' NOT NULL',
                ],
                'key' => [
                    'KEY stub_index' => 'stub',
                ],
            ],
        ]);

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