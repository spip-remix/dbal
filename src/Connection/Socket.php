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
final class Socket extends File implements ConnectionInterface
{
    /** @var resource|\Pdo|null */
    protected mixed $handle;

    /** @var resource|\Pdo|null */
    protected mixed $alter_handle;

    /** @var resource|\Pdo|null */
    protected mixed $readonly_handle;

    /**
     * @param non-empty-string $socket
     * @param non-empty-string $driver
     * @param non-empty-string $schema
     * @param string $username
     * @param string $password
     * @param string[] $options
     */
    public function __construct(
        string $socket,
        string $driver,
        string $schema,
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
    ) {
        if (\str_contains($driver, 'mysql')) {
            $socket = 'unix_socket=' . $socket;
        }
        if (\str_contains($driver, 'pgsql')) {
            $socket = 'host=/tmp;port=5432; (' . $socket . ')';
        }
        $base = 'dbname=' . $schema;
        parent::__construct($driver, $socket . ';' . $base . ';');
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
