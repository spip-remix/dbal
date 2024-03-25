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
