<?php

declare(strict_types=1);

/**
 * SPIP-Remix, Système de publication pour l'internet, mais remixé...
 *
 * Copyright © avec timidité depuis 2018 - JamesRezo
 *
 * Ce programme est un logiciel libre distribué sous licence MIT ou GNU/GPL, ça dépend des fois.
 */

namespace SpipRemix\Component\Dbal\Connection;

/**
 * Interface de connection SQL.
 *
 * @todo Gérer d'autres paramètres pour la chaîne de connexion (ex: charset)
 *
 * @author JamesRezo <james@rezo.net>
 */
interface ConnectionInterface
{
    /**
     * Instantier une connection à l'aide d'une chaîne de type URI valide.
     *
     * @param non-empty-string $uri
     */
    public static function fromUri(string $uri): self;

    /**
     * Chaîne de connection via une extension PHP pdo_*.
     *
     * @return non-empty-string
     */
    public function getPdoString(): string;

    /**
     * Connection au schéma par défaut.
     */
    public function connect(): static;

    /**
     * Connection au schéma utilisée si un couple
     * username/password dédié aux changements de schéma
     * est fourni.
     */
    public function alter_connect(): static;

    /**
     * Connection au schéma utilisée si un couple
     * username/password dédié à la lecture seule
     * est fourni.
     */
    public function readonly_connect(): static;
}
