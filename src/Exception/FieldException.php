<?php

declare(strict_types=1);

/**
 * SPIP-Remix, Système de publication pour l'internet, mais remixé...
 *
 * Copyright © avec timidité depuis 2018 - JamesRezo
 *
 * Ce programme est un logiciel libre distribué sous licence MIT ou GNU/GPL, ça dépend des fois.
 */

namespace SpipRemix\Component\Dbal\Exception;

/**
 * Undocumented class.
 *
 * @codeCoverageIgnore
 *
 * @author JamesRezo <james@rezo.net>
 */
final class FieldException extends AbstractDbalException
{
    protected static function register(): void
    {
        self::$parameters = ['name', 'dataType'];
    }

    protected static function prepareMessage(string ...$context): string
    {
        return sprintf(
            'Un champ doit avoir un nom et un dataType valide. "%s" et "%s" donnés',
            $context['name'],
            $context['dataType'],
        );
    }
}
