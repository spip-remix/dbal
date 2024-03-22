<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Fixtures;

use SpipRemix\Component\Dbal\FieldInterface;
use SpipRemix\Component\Dbal\SchemaInterface;
use SpipRemix\Component\Dbal\TableInterface;

class StubTable implements TableInterface
{
    public function getSchema(): ?SchemaInterface
    {
        return \null;
    }

    public function setSchema(SchemaInterface $schema): TableInterface
    {
        return $this;
    }

    public function getName(): string
    {
        return 'stub';
    }

    public function getFullname(): string
    {
        return '';
    }

    public function getPrefixedName(): string
    {
        return '';
    }

    public function getFields(): array
    {
        return [];
    }

    public function getField(string $name): ?FieldInterface
    {
        return \null;
    }

    public function addField(FieldInterface $field): TableInterface
    {
        return $this;
    }
}