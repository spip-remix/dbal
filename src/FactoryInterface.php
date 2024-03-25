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
 * Undocumented interface.
 *
 * @author JamesRezo <james@rezo.net>
 */
interface FactoryInterface
{
    /**
     * Undocumented function.
     *
     * @param non-empty-string ...$parameters
     *
     * @return SchemaInterface
     */
    public function createSchema(string ...$parameters): SchemaInterface;

    /**
     * Undocumented function.
     *
     * @param non-empty-string ...$parameters
     *
     * @return TableInterface
     */
    public function createTable(string ...$parameters): TableInterface;

    /**
     * Undocumented function.
     *
     * @param string[]|string $parameters
     */
    public function createField(array|string|bool ...$parameters): FieldInterface;
}
