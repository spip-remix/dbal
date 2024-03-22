<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal;

class Schema implements SchemaInterface
{
    /**
     * @var array<string,TableInterface> $tables
     */
    private array $tables = [];

    public function __construct(
        private string $name,
        private string $prefix = 'spip',
    ) {
        if ($name == '') {
            throw new \Exception('Un schéma doit avoir un nom.');
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getTables(): array
    {
        return $this->tables;
    }

    public function getTable(string $name): ?TableInterface
    {
        if (\array_key_exists($name, $this->tables)) {
            return $this->tables[$name];
        }

        return null;
    }

    public function addTable(TableInterface $table): SchemaInterface
    {
        if (\array_key_exists($table->getName(), $this->tables)) {
            throw new \Exception(sprintf('Une table portant le nom "%s" existe déjà.', $table->getName()));
        }

        $table->setSchema($this);
        $this->tables[$table->getName()] = $table;

        return $this;
    }
}
