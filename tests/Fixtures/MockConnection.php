<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Fixtures;

use SpipRemix\Component\Dbal\Connection\ConnectionInterface;

class MockConnection implements ConnectionInterface
{
    public static function fromUri(string $uri): self
    {
        return new self();
    }

    public function getPdoString(): string
    {
        return 'mock:yo';
    }

    public function connect(): static
    {
        return new static();
    }

    public function alter_connect(): static
    {
        return new static();
    }

    public function readonly_connect(): static
    {
        return new static();
    }
}
