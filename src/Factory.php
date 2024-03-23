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
 * Undocumented class
 *
 * @author JamesRezo <james@rezo.net>
 */
class Factory implements FactoryInterface
{
    public function createSchema(string ...$parameters): SchemaInterface
    {
        if (!isset($parameters['name'], $parameters['prefix'])) {
            throw new \Exception('Un schéma doit avoir un nom et un préfixe.');
        }

        return new Schema($parameters['name'], $parameters['prefix']);
    }

    public function createTable(string ...$parameters): TableInterface
    {
        if (!isset($parameters['name'])) {
            throw new \Exception('Une table doit avoir un nom.');
        }

        return new Table($parameters['name']);
    }

    public function createField(string|bool|null ...$parameters): FieldInterface
    {
        if (!isset($parameters['name'], $parameters['dataType'])) {
            throw new \Exception('Un champ doit avoir et nom et un dataType valide.');
        }

        /** @var array{name:non-empty-string,dataType:non-empty-string,default?:?non-empty-string,nullable?:bool} $parameters */
        return new Field(...$parameters);
    }
}
