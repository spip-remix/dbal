<?php

declare(strict_types=1);

/**
 * SPIP-Remix, Système de publication pour l'internet, mais remixé...
 *
 * Copyright © avec timidité depuis 2018 - JamesRezo
 *
 * Ce programme est un logiciel libre distribué sous licence MIT ou GNU/GPL, ça dépend des fois.
 */

namespace SpipRemix\Component\Dbal;

/**
 * Undocumented class.
 *
 * @author JamesRezo <james@rezo.net>
 */
class Table implements TableInterface
{
    /**
     * @var SchemaInterface|null
     */
    private ?SchemaInterface $schema = null;

    /**
     * @var array<string,FieldInterface> $fields
     */
    private array $fields = [];

    public function __construct(
        private string $name,
    ) {
        if ($name == '') {
            throw new \Exception('Une table doit avoir un nom.');
        }
    }

    public function getSchema(): ?SchemaInterface
    {
        return $this->schema;
    }

    public function setSchema(SchemaInterface $schema): TableInterface
    {
        $this->schema = $schema;

        return $this;
    }

    public function getName(): string
    {
        /** @var non-empty-string */
        return $this->name;
    }

    public function getFullname(): string
    {
        $name = $this->getSchema()?->getName();

        return ($name ? $name . '.' : '') . $this->getPrefixedName();
    }

    public function getPrefixedName(): string
    {
        $prefix = $this->getSchema()?->getPrefix();

        return ($prefix ? $prefix . '_' : '') . $this->getName();
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getField(string $name): ?FieldInterface
    {
        if (\array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }

        return null;
    }

    public function addField(FieldInterface $field): TableInterface
    {
        if (\array_key_exists($field->getName(), $this->fields)) {
            throw new \Exception(sprintf('Un champ portant le nom "%s" existe déjà.', $field->getName()));
        }

        $field->setTable($this);
        $this->fields[$field->getName()] = $field;

        return $this;
    }
}
