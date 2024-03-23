<?php

/**
 * SPIP, Système de publication pour l'internet
 *
 * Copyright © avec tendresse depuis 2001
 * Arnaud Martin, Antoine Pitrou, Philippe Rivière, Emmanuel Saint-James
 *
 * Ce programme est un logiciel libre distribué sous licence GNU/GPL.
 */

namespace SpipRemix\Component\Dbal\Sqlite;

/**
 * Pouvoir retrouver le PDO utilisé pour générer un résultat de requête.
 */
final class PDOStatement extends \PDOStatement
{
    private function __construct(private \PDO &$PDO) {}

    public function getPDO(): \PDO
    {
        return $this->PDO;
    }
}
