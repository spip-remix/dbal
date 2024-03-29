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

use SpipRemix\Component\Dbal\Exception\FileException;

/**
 * Undocumented class.
 *
 * @author JamesRezo <james@rezo.net>
 */
class File implements ConnectionInterface
{
    /** @var non-empty-string */
    protected string $pdoString;

    /** @var non-empty-string */
    protected string $uriString;

    /**
     * @param non-empty-string $driver
     * @param string $filename
     */
    public function __construct(
        string $filename,
        string $driver,
    ) {
        $driver = \strtolower($driver);
        $realFilename = \realpath($filename);
        if (\str_contains($driver, 'sqlite') && \in_array($filename, [':memory:', ''])) {
            $realFilename = $filename;
        }

        /**
         * @todo tests
         * dossier qui n'existe pas
         * dossier existe mais accessible en lecture seule
         * fichier qui existe mais accessible en lecture seule
         * fichier qui n'existe pas mais dans un dossier valide
         * fichier qui existe et writeable
         */
        if (
            $realFilename === false
            // && \is_writable($realFilename)
            // && (\is_dir(\dirname($realFilename)) && \is_writable(\dirname($realFilename)))
        ) {
            FileException::throw(...['filename' => $filename]);
        }

        $driver = \in_array($driver, ['sqlite3', 'pdo_sqlite']) ?
            'sqlite' :
            $driver;

        $this->pdoString = $driver . ':' . $realFilename;
        // $this->uriString = 'file://' . $realFilename; // Ou memory:// ou temp://
    }

    public static function fromUri(string $uri): ConnectionInterface
    {
        return new self(':memory:', 'pdo_sqlite');
    }

    public function getPdoString(): string
    {
        return $this->pdoString;
    }

    public function connect(): static
    {
        return $this;
    }

    public function alter_connect(): static
    {
        return $this->connect();
    }

    public function readonly_connect(): static
    {
        return $this->connect();
    }
}
