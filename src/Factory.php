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
use SpipRemix\Component\Dbal\Connection\ConnectionTrait;
use SpipRemix\Component\Dbal\Connection\File;
use SpipRemix\Component\Dbal\Connection\Socket;
use SpipRemix\Component\Dbal\Exception\ConnectionException;
use SpipRemix\Component\Dbal\Exception\DriverException;
use SpipRemix\Component\Dbal\Exception\FieldException;
use SpipRemix\Component\Dbal\Exception\SchemaException;
use SpipRemix\Component\Dbal\Exception\TableException;

/**
 * Undocumented class
 *
 * @author JamesRezo <james@rezo.net>
 */
class Factory implements FactoryInterface
{
    use ConnectionTrait;

    public const KNOWN_DRIVERS = [
        'pdo_mysql' => '', // MySqlConnector::class,
        'pdo_sqlite' => '', // SqliteConnector::class,
        'pdo_pgsql' => '', // PgSqlConnector::class,
        'mysqli' => '', // MySqlConnector::class,
        'sqlite3' => '', // SqliteConnector::class,
        'pgsql' => '', // PgSqlConnector::class,
    ];

    public const DEFAULTS = [
        'hostname' => 'localhost',
        'mysql_port' => 3306,
        'pgsql_port' => 5432,
        'mysql_socket' => '/tmp/mysql.sock',
        'pgsql_socket' => '/tmp/.s.PGSQL.5432',
        'filename' => 'config/bases/%schema%.sqlite',
        'schema' => 'spip',
        'username' => 'spip',
        'password' => 'spip',
    ];

    public function __construct(private ?string $rootDir = null)
    {
        if (\is_null($rootDir)) {
            $this->rootDir = \rtrim(\dirname($_SERVER['SCRIPT_FILENAME']), '/') . '/';
        }
    }

    public function createConnectionFromArray(array $connection): ConnectionInterface
    {
        $schema = $connection['schema'] ?? 'spip';

        $this->extensionDetector();
        if (!\in_array($connection['driver'], $this->installedExtensions)) {
            DriverException::throw(...['name' => $connection['driver']]);
        }

        // SQLite
        if (isset($connection['filename'])) {
            $filename = $this->rootDir . \str_replace('%schema%', $schema, $connection['filename']);

            return new File($filename, $connection['driver']);
        }

        // Socket
        if (isset($connection['socket'])) {
            return new Socket($connection['socket'], $connection['driver'], $schema);
        }

        // TCP
        if (isset($connection['hostname'])) {
        }

        ConnectionException::throw();
    }

    public function createSchemaFromArray(array $definitions): SchemaInterface
    {
        return new Schema('spip');
    }

    public function createSchema(string ...$parameters): SchemaInterface
    {
        if (!isset($parameters['name'], $parameters['prefix'])) {
            SchemaException::throw(...$parameters);
        }

        return new Schema($parameters['name'], $parameters['prefix']);
    }

    public function createTable(string ...$parameters): TableInterface
    {
        if (!isset($parameters['name'])
            || empty($parameters['name'])
        ) {
            TableException::throw($parameters['name']);
        }

        return new Table($parameters['name']);
    }

    public function createField(array|string|bool ...$parameters): FieldInterface
    {
        if (!isset($parameters['name'], $parameters['dataType'])
            || (!is_string($parameters['name']) || empty($parameters['name']))
            || (!is_string($parameters['dataType']) || empty($parameters['dataType']))
            || (isset($parameters['default']) && empty($parameters['default']))
            || (isset($parameters['nullable']) && !\is_bool($parameters['nullable']))
        ) {
            FieldException::throw(...$parameters);
        }

        /** @var array{name:non-empty-string,dataType:non-empty-string,default?:non-empty-string,nullable?:bool} $parameters */
        return new Field(...$parameters);
    }
}
