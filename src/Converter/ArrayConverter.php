<?php

declare(strict_types=1);

/**
 * SPIP-Remix, Système de publication pour l'internet, mais remixé...
 *
 * Copyright © avec timidité depuis 2018 - JamesRezo
 *
 * Ce programme est un logiciel libre distribué sous licence MIT ou GNU/GPL, ça dépend des fois.
 */

namespace SpipRemix\Component\Dbal\Converter;

use SpipRemix\Component\Dbal\FactoryInterface;
use SpipRemix\Component\Dbal\Field;
use SpipRemix\Component\Dbal\Schema;
use SpipRemix\Component\Dbal\SchemaInterface;
use SpipRemix\Component\Dbal\Table;

/**
 * Undocumented class.
 *
 * @template T of array{name:non-empty-string,prefix:non-empty-string,tables:list{array{name:non-empty-string,fields:list{array{name:non-empty-string,dataType:non-empty-string,default?:?non-empty-string,nullable?:bool}}}}}
 *
 * @author JamesRezo <james@rezo.net>
 */
class ArrayConverter implements ConverterInterface
{
    /** @var T */
    private array $arraySchema;

    /**
     * @param T $arraySchema
     */
    public function __construct(
        private FactoryInterface $factory,
        array $arraySchema,
    ) {
        $this->arraySchema = $arraySchema;
    }

    public function convert(): SchemaInterface
    {
        $schema = $this->factory->createSchema(...[
            'name' => $this->arraySchema['name'],
            'prefix' => $this->arraySchema['prefix']
        ]);
        foreach ($this->arraySchema['tables'] as $arrayTable) {
            $table = $this->factory->createTable(...['name' => $arrayTable['name']]);
            foreach ($arrayTable['fields'] as $arrayField) {
                $table->addField($this->factory->createField(...$arrayField));
            }
            $schema->addTable($table);
        }

        return $schema;
    }
}
