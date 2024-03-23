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
 * SQL Schema definition for SPIP.
 *
 * @author JamesRezo <james@rezo.net>
 */
interface SchemaInterface
{
    /**
     * Get the name of the schema.
     *
     * SHOULD be snake cased (significant_name)
     * ascii letters, not dot, lower case and
     * underscore `_` to separate significant words
     * Defaults to 'spip'
     *
     * @return non-empty-string
     */
    public function getName(): string;

    /**
     * Undocumented function.
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Undocumented function.
     *
     * @return array<string,TableInterface>
     */
    public function getTables(): array;

    /**
     * Undocumented function.
     */
    public function getTable(string $name): ?TableInterface;

    /**
     * @todo charset, collation, default-engine(mysql/mariadb) ?
     * @todo timezone, version ?
     */

    /**
     * Adds a table to the schema.
     */
    public function addTable(TableInterface $table): self;
}
