<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Unit;

use PHPUnit\Framework\TestCase as FrameworkTestCase;
use SpipRemix\Component\Dbal\TableInterface;
use SpipRemix\Component\Dbal\Test\Fixtures\StubTable;

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
        return new StubTable;
    }
}
