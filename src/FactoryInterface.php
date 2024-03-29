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

use SpipRemix\Component\Dbal\Connection\ConnectionInterface;

/**
 * Fabrique d'objets de base de données.
 *
 * @author JamesRezo <james@rezo.net>
 */
interface FactoryInterface
{
    /**
     * Créer une connection SQL.
     *
     * @example https://github.com/spip-remix/dbal/blob/0.1/docs/Connecteurs.md#configuration Exemples de configuration
     *
     * @param array{
     *      driver:non-empty-string,
     *      schema?:non-empty-string,
     *      hostname?:non-empty-string,
     *      port?:non-negative-int,
     *      socket?:non-empty-string,
     *      filename?:non-empty-string,
     *      username?:?string,
     *      password?:?string,
     *      alter_username?:?string,
     *      alter_password?:?string,
     *      readonly_username?:?string,
     *      readonly_password?:?string,
     * } $connection
     */
    public function createConnectionFromArray(array $connection): ConnectionInterface;

    /**
     * Créer un schéma à partir d'un tableau PHP.
     *
     * @param array{
     *  name:non-empty-string,
     *  prefix:non-empty-string,
     *  tables?:list{
     *      name:non-empty-string,
     *      fields:list{array{
     *          name:non-empty-string,
     *          dataType:non-empty-string,
     *          default?:non-empty-string,
     *          nullable?:bool,
     *      }},
     *      keys?:list{array{
     *          name:non-empty-string,
     *          dataType:non-empty-string,
     *      }},
     *  },
     * } $definitions
     *
     * @return SchemaInterface
     */
    public function createSchemaFromArray(array $definitions): SchemaInterface;

    /**
     * Instancier un schéma (un base).
     *
     * @param non-empty-string ...$parameters
     *
     * @return SchemaInterface
     */
    public function createSchema(string ...$parameters): SchemaInterface;

    /**
     * Instancier une table.
     *
     * @param non-empty-string ...$parameters
     *
     * @return TableInterface
     */
    public function createTable(string ...$parameters): TableInterface;

    /**
     * Instancier une colonne (un champ).
     *
     * @param string[]|string $parameters
     */
    public function createField(array|string|bool ...$parameters): FieldInterface;
}
