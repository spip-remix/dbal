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

use SpipRemix\Component\Dbal\Field;
use SpipRemix\Component\Dbal\Schema;
use SpipRemix\Component\Dbal\SchemaInterface;
use SpipRemix\Component\Dbal\Table;

/**
 * Undocumented interface.
 *
 * @author JamesRezo <james@rezo.net>
 */
interface ConverterInterface
{
    /**
     * Undocumented function.
     */
    public function convert(): SchemaInterface;
}
