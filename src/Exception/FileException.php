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
 * @author JamesRezo <jaames@rezo.net>
 */
final class FileException extends AbstractDbalException
{
    protected static function register(): void
    {
        self::$parameters = ['filename'];
    }

    protected static function prepareMessage(string ...$context): string
    {
        return sprintf(
            'Un fichier doit avoir un nom valide. "%s" donné',
            $context['filename'],
        );
    }
}
