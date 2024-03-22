<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Fixtures;

use SpipRemix\Component\Dbal\FieldInterface;
use SpipRemix\Component\Dbal\TableInterface;

class StubField implements FieldInterface
{
    public function getName(): string
    {
        return 'stub';
    }

    public function getFullName(): string
    {
        return '';
    }

    public function getFullFullName(): string
    {
        return '';
    }

    public function getDataType(): string
    {
        return '';
    }

    public function getDefault(): ?string
    {
        return null;
    }

    public function getNullable(): bool
    {
        return false;
    }

    public function getTable(): TableInterface
    {
        return new StubTable();
    }

    public function setTable(TableInterface $table): FieldInterface
    {
        return $this;
    }
}
