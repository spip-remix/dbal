<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit;

use PHPUnit\Framework\TestCase as FrameworkTestCase;
use SpipRemix\Component\Dbal\TableInterface;
use SpipRemix\Component\Dbal\Test\Fixtures\FakeDetector;
use SpipRemix\Component\Dbal\Test\Fixtures\StubTable;
use Symfony\Component\Filesystem\Filesystem;

class TestCase extends FrameworkTestCase
{
    private array $schema;

    public function getSchema(): array
    {
        if (empty($this->schema)) {
            $schema = require __DIR__ . '/../Fixtures/schema.php';
        }

        return $schema;
    }

    public function getTable(): TableInterface
    {
        return new StubTable();
    }

    public function createFile(): string
    {
        $fs = new Filesystem;
        $fs->mkdir('tmp');
        $filename = $fs->tempnam('tmp', 'spip-remix', '.sqlite');

        return $filename;
    }

    public function cleanFiles(): void
    {
        $fs = new Filesystem;
        $fs->remove('tmp');
    }

    public function getDetector($extensions): mixed
    {
        return new FakeDetector($extensions);
    }
}
