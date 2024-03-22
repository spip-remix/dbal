<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Fixtures;

use SpipRemix\Component\Dbal\SchemaInterface;
use SpipRemix\Component\Dbal\TableInterface;

class StubSchema implements SchemaInterface
{
    public function getName(): string
    {
        return 'stub';
    }

    public function getPrefix(): string
    {
        return '';
    }

    public function getTables(): array
    {
        return ['stub' => new StubTable];
    }

    public function getTable(string $name): ?TableInterface
    {
        return null;
    }

    public function addTable(TableInterface $table): SchemaInterface
    {
        $table->setSchema($this);

        return $this;
    }
}
