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

use SpipRemix\Contracts\Exception\ExceptionInterface;

/**
 * Undocumented class.
 */
abstract class AbstractDbalException extends \UnexpectedValueException implements ExceptionInterface
{
    /** @var string[] $parameters */
    public static array $parameters;

    abstract protected static function register(): void;

    abstract protected static function prepareMessage(string ...$context): string;

    /**
     * Undocumented function.
     *
     * @param string[]|string|bool ...$context
     */
    public static function throw(array|string|bool ...$context): never
    {
        $class = \get_called_class();
        \call_user_func([$class, 'register']);

        if (!self::validate(...$context)) {
            throw new \UnexpectedValueException('Paramètres invalides.');
        }
        $context = self::normalize(...$context);

        throw new $class(static::prepareMessage(...$context));
    }

    /**
     * @param string[]|string|bool ...$context
     */
    protected static function validate(array|string|bool ...$context): bool
    {
        foreach (self::$parameters as $parameter) {
            if (!\array_key_exists($parameter, $context)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string[]|string|bool ...$context
     *
     * @return array<string,string>
     */
    protected static function normalize(array|string|bool ...$context): array
    {
        $normalized = [];

        foreach ($context as $key => $value) {
            $normalized[(string) $key] = (
                \is_bool($value) ?
                    var_export($value, true) :
                    (string) (
                        \is_array($value) ?
                            \array_shift($value) :
                            $value
                    )
            );
        }

        return $normalized;
    }
}
