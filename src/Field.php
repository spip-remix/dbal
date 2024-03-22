<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal;

/**
 * SQL Column(field) definition for SPIP.
 *
 * @author JamesRezo <james@rezo.net>
 */
class Field implements FieldInterface
{
    private TableInterface $table;

    public function __construct(
        private string $name,
        private string $dataType,
        private ?string $default = \null,
        private bool $nullable = \false,
    ) {
        if ($name == '' || $dataType == '') {
            throw new \Exception('Un champ doit avoir un nom et un dataType.');
        }
    }

    /**
     * Get the name of the field.
     *
     * SHOULD be snake cased (significant_name)
     * ascii letters, not dot, lower case and
     * underscore `_` to separate significant words
     *
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getFullName(): string
    {
        return $this->table->getPrefixedName() . '.' . $this->getName();
    }

    public function getFullFullName(): string
    {
        return $this->table->getFullname() . '.' . $this->getName();
    }

    public function getDataType(): string
    {
        return $this->dataType;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }

    public function getTable(): TableInterface
    {
        return $this->table;
    }

    public function setTable(TableInterface $table): self
    {
        $this->table = $table;

        return $this;
    }
}
