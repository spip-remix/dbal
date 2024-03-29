<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit\Connection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use SpipRemix\Component\Dbal\Connection\File;
use SpipRemix\Component\Dbal\Exception\AbstractDbalException;
use SpipRemix\Component\Dbal\Exception\FileException;
use SpipRemix\Component\Dbal\Test\Unit\TestCase;

#[CoversClass(File::class)]
#[CoversClass(AbstractDbalException::class)]
class FileTest extends TestCase
{
    private string $tmpFilename;

    public function setUp(): void
    {
        $this->tmpFilename = $this->createFile();
    }

    public function tearDown(): void
    {
        $this->cleanFiles();
    }

    public static function dataSqliteInstantiation(): array
    {
        return [
            'temporary-sqlite' => [
                'expectedPdoString' => 'sqlite:',
                'filename' => '',
                'driver' => 'pdo_sqlite',
            ],
            'memory-sqlite' => [
                'expectedPdoString' => 'sqlite::memory:',
                'filename' => ':memory:',
                'driver' => 'pdo_sqlite',
            ],
            'file-sqlite' => [
                'expectedPdoString' => \null,
                'filename' => \null,
                'driver' => 'pdo_sqlite',
            ],
        ];
    }

    #[DataProvider('dataSqliteInstantiation')]
    public function testSqliteInstantiation($expectedPdoString, $filename, $driver): void
    {
        // Given
        if ($filename === \null) {
            $filename = $this->tmpFilename;
            $expectedPdoString = 'sqlite:' . \realpath($filename);
        }
        $file = new File($filename, $driver);

        // When
        $actualPdoString = $file->getPdoString();

        // Then
        $this->assertEquals($expectedPdoString, $actualPdoString);
    }

    public function testFailedSqliteInstantiation(): void
    {
        // Given
        $this->expectException(FileException::class);
        $this->expectExceptionMessage('Un fichier doit avoir un nom valide. "not-exists" donné');

        // When
        $file = new File('not-exists', 'sqlite3');

        // Then
        // Throws a file exception
    }
}
