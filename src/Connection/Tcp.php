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
 * Undocumented interface.
 *
 * @author JamesRezo <james@rezo.net>
 */
final class Tcp implements ConnectionInterface
{
    /** @var resource|\Pdo|null */
    protected mixed $handle;

    /** @var resource|\Pdo|null */
    protected mixed $alter_handle;

    /** @var resource|\Pdo|null */
    protected mixed $readonly_handle;

    /** @var non-empty-string */
    protected string $uriString;

    /** @var non-empty-string */
    private string $pdoString;

    /**
     * @param non-empty-string $driver
     * @param non-empty-string $hostname
     * @param non-empty-string $base
     * @param positive-int|null $port
     * @param string $username
     * @param string $password
     * @param string[] $options
     * @param ?string $alter_username
     * @param ?string $alter_password
     * @param ?non-empty-string $uriString
     */
    public function __construct(
        string $hostname,
        string $driver,
        string $base,
        ?int $port = null,
        private string $username = '',
        #[\SensitiveParameter]
        private string $password = '',
        protected array $options = [],
        private ?string $alter_username = null,
        #[\SensitiveParameter]
        private ?string $alter_password = null,
        private ?string $readonly_username = null,
        #[\SensitiveParameter]
        private ?string $readonly_password = null,
        ?string $uriString = \null,
    ) {
        $port = \is_null($port) ? '' : 'port=' . \strval($port) . ';';
        $this->pdoString = $driver . ':host=' . $hostname . ';' . $port . 'dbname=' . $base . ';';
        $this->uriString = $uriString ?? 'N/A';
    }

    public static function fromUri(string $uri): ConnectionInterface
    {
        return new static('localhost', 'pdo_pgsql', 'spip', uriString: $uri ?: 'N/A');
    }

    public function getPdoString(): string
    {
        return $this->pdoString;
    }

    public function connect(): static
    {
        if (!isset($this->handle)) {
            $this->handle = new \PDO(
                $this->getPdoString(),
                $this->username,
                $this->password,
                $this->options
            );
        }

        return $this;
    }

    public function alter_connect(): static
    {
        if (!isset($this->alter_handle)) {
            $this->alter_handle = new \PDO(
                $this->getPdoString(),
                $this->alter_username,
                $this->alter_password,
                $this->options
            );
        }

        return $this;
    }

    public function readonly_connect(): static
    {
        if (!isset($this->readonly_handle)) {
            $this->readonly_handle = new \PDO(
                $this->getPdoString(),
                $this->readonly_username,
                $this->readonly_password,
                $this->options
            );
        }

        return $this;
    }
}
