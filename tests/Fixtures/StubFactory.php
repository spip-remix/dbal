<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Fixtures;

use SpipRemix\Component\Dbal\FactoryInterface;
use SpipRemix\Component\Dbal\FieldInterface;
use SpipRemix\Component\Dbal\SchemaInterface;
use SpipRemix\Component\Dbal\TableInterface;

/**
 * Undocumented class
 *
 * @author JamesRezo <james@rezo.net>
 */
class StubFactory implements FactoryInterface
{
    public function createSchema(string ...$parameters): SchemaInterface
    {
        return new StubSchema;
    }

    public function createTable(string ...$parameters): TableInterface
    {
        return new StubTable;
    }

    public function createField(string|bool|null ...$parameters): FieldInterface
    {
        return new StubField;
    }
}
