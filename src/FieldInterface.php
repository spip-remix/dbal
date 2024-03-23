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

    /**
     * Undocumented function
     *
     * @return non-empty-string
     */
    public function getFullName(): string;

    /**
     * Undocumented function
     *
     * @return non-empty-string
     */
    public function getFullFullName(): string;

    /**
     * Undocumented function
     *
     * @return non-empty-string
     */
    public function getDataType(): string;

    /**
     * Undocumented function
     *
     * @return non-empty-string|null
     */
    public function getDefault(): ?string;

    /**
     * Undocumented function.
     */
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
