<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal;

/**
 * SQL Field definition for SPIP.
 *
 * @author JamesRezo <james@rezo.net>
 */
interface FieldInterface
{
    /**
     * Get the name of the field.
     *
     * SHOULD be snake cased (significant_name)
     * ascii letters, not dot, lower case and
     * underscore `_` to separate significant words
     *
     * @return non-empty-string
     */
    public function getName(): string;

    public function getFullName(): string;

    public function getFullFullName(): string;

    public function getDataType(): string;

    public function getDefault(): ?string;

    public function getNullable(): bool;

    /**
     * Get the Table.
     *
     * SHOULD be snake cased (tbl_name)
     *
     * @return TableInterface
     */
    public function getTable(): TableInterface;

    /**
     * Undocumented function.
     */
    public function setTable(TableInterface $table): self;

    /**
     * @todo constraint, ...
     */
}
