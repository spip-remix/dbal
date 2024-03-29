<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit\Connection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use SpipRemix\Component\Dbal\Connection\ConnectionTrait;
use SpipRemix\Component\Dbal\Test\Unit\TestCase;

#[CoversClass(ConnectionTrait::class)]
class ConnectionTraitTest extends TestCase
{
    public static function dataExtensionDetector(): array
    {
        return [
            'no-extensions' => [
                'expected' => ['brands' => [], 'installed' => []],
                'extensions' => [],
            ],
            'only-pgsql' => [
                'expected' => ['brands' => ['pgsql'], 'installed' => ['pgsql']],
                'extensions' => ['pgsql'],
            ],
            'minimal-spip42-extensions' => [
                'expected' => ['brands' => ['sqlite', 'mysql'], 'installed' => ['pdo_sqlite', 'mysqli']],
                'extensions' => ['pdo_sqlite', 'mysqli'],
            ],
        ];
    }

    #[DataProvider('dataExtensionDetector')]
    public function testExtensionDetector($expected, $extensions): void
    {
        // Given
        $detector = $this->getDetector($extensions);

        // When
        $actual = $detector->get();

        // Then
        $this->assertEquals($expected, $actual);
    }
}
