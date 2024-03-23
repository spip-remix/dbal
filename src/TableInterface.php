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
 * SQL Table definition for SPIP.
 *
 * @author JamesRezo <james@rezo.net>
 */
interface TableInterface
{
    /**
     * Get the name of the schema or base.
     *
     * MAY be empty ('')
     * SHOULD be snake cased (my_schema)
     * Defaults to 'spip'
     *
     * @return SchemaInterface
     */
    public function getSchema(): ?SchemaInterface;

    /**
     * Undocumented function.
     */
    public function setSchema(SchemaInterface $schema): self;

    /**
     * Get the name of the table.
     *
     * SHOULD be snake cased (significant_name)
     * ascii letters, not dot, lower case and
     * underscoere `_` to separate significant words
     *
     * @return non-empty-string
     */
    public function getName(): string;

    /**
     * Get the concated schema and table naames.
     *
     * SHOULD be equal to :
     *
     * `tbl_name` (the prefixed table name) if schema is empty,
     * `schema.tbl_name` if not.
     *
     * @return non-empty-string
     */
    public function getFullname(): string;

    /**
     * Undocumented function.
     *
     * @return non-empty-string
     */
    public function getPrefixedName(): string;

    /**
     * Undocumented function.
     *
     * @return array<string,FieldInterface>
     */
    public function getFields(): array;

    /**
     * Undocumented function.
     */
    public function getField(string $name): ?FieldInterface;

    /**
     * Adds a field to the table.
     */
    public function addField(FieldInterface $field): self;
}
