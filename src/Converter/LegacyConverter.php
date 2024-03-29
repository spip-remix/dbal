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

/**
 * Undocumented class.
 *
 * @template T of array<non-empty-string,array{
 *      field:array<non-empty-string,string>,
 *      key?:array<non-empty-string,string>,
 * }>
 * @template U of array{
 *  name:non-empty-string,
 *  prefix:non-empty-string,
 *  tables:list{array{
 *      name:non-empty-string,
 *      fields:list{array{
 *          name:non-empty-string,
 *          dataType:non-empty-string,
 *          default?:?non-empty-string,
 *          nullable?:bool,
 *      },
 *      keys?:list{array{
 *          name:non-empty-string,
 *          dataType:non-empty-string,
 *      }},
 *  }},
 * }}
 * @extends ArrayConverter<U>
 *
 * @author JamesRezo <james@rezo.net>
 */
class LegacyConverter extends ArrayConverter
{
    /**
     * @param T $arraySchema
     * @param non-empty-string $prefix
     */
    public function __construct(
        FactoryInterface $factory,
        array $arraySchema,
        private string $schema = 'spip',
        private string $prefix = 'spip',
    ) {
        $this->prefix = $prefix ?: 'spip';
        parent::__construct($factory, $this->fromLegacy($arraySchema));
    }

    /**
     * Undocumented function.
     *
     * @param T $legacy
     *
     * @return U
     */
    private function fromLegacy(array $legacy): array
    {
        $liste = array_keys($legacy);

        $converted = [
            'name' => $this->schema,
            'prefix' => $this->prefix,
            'tables' => [],
        ];
        foreach ($liste as $table) {
            $fields = [];
            foreach($legacy[$table]['field'] as $name => $dataType) {
                $fields[] = ['name' => $name, 'dataType' => $dataType];
            }
            $keys = [];
            if (isset($legacy[$table]['key'])) {
                foreach($legacy[$table]['key'] as $dataType => $name) {
                    $keys[] = ['name' => $name, 'dataType' => $dataType];
                }
            }
            $converted['tables'][] = [
                'name' => str_replace('spip_', '', $table),
                'fields' => $fields,
                'keys' => $keys,
            ];
        }

        /** @var U $converted */
        return $converted;
    }
}
