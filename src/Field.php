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
        $exploded = $this->explodeDatatype($name, $dataType);
        if (\is_null($exploded)) {
            throw new \Exception('Un champ doit avoir et nom et un dataType valide.');
        }

        $this->default = $default ?? ($exploded['default'] ?? \null);
        $this->nullable = $nullable ?: ($exploded['nullable'] ?? \false);
    }

    public function getName(): string
    {
        /** @var non-empty-string */
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
        /** @var non-empty-string */
        return $this->dataType;
    }

    public function getDefault(): ?string
    {
        /** @var non-empty-string|null */
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

    /**
     * Convert the dataType into $dataType, $default, $nullable..
     *
     * @param string $dataType
     *
     * @return array{name:non-empty-string,dataType:non-empty-string,default?:?non-empty-string,nullable?:bool}|null
     */
    private function explodeDatatype(string $name, string $dataType): ?array
    {
        if ($name == '' || $dataType == '') {
            return null;
        }
        $formattedDataType = ['name' => $name];

        $nullable = str_contains(strtoupper($dataType), 'NOT NULL');
        $dataType = (string) preg_replace(', ?NOT NULL,i', '', $dataType);
        $default = str_contains(strtoupper($dataType), 'DEFAULT');
        if ($default && preg_match(',DEFAULT (.+) ?,i', $dataType, $matches)) {
            /** @var list{non-empty-string,non-empty-string} $matches */
            $formattedDataType['default'] = $matches[1];
            $dataType = (string) preg_replace(',' . $matches[0] . ',i', '', $dataType);
        }
        $dataType = trim($dataType);
        if ($dataType == '') {
            return null;
        }
        $formattedDataType['dataType'] = $dataType;
        $formattedDataType['nullable'] = $nullable;

        return $formattedDataType;
    }
}
