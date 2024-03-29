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
 * Méthodes communes aux connections SQL.
 *
 * @author JamesRezo <james@rezo.net>
 */
trait ConnectionTrait
{
    // public const ALL_BRANDS = ['mysql', 'sqlite', 'pgsql', ]; // PHP8.2
    /** @var string[] */
    protected array $ALL_BRANDS = ['mysql', 'sqlite', 'pgsql', ];

    // public const ALL_EXTENSIONS = [ // PHP8.2
    /** @var string[] */
    public array $ALL_EXTENSIONS = [
        // \PDO drivers
        'pdo_mysql', 'pdo_sqlite', 'pdo_pgsql',
        // vendor drivers
        'mysqli', 'mysqlnd', 'pgsql', 'sqlite3',
    ];

    /** @var string[] */
    protected array $brands = [];

    /** @var string[] */
    protected array $installedExtensions = [];

    /**
     * @internal Pour les T.U. : Tricher sur les extensions présentes dans PHP.
     *
     * @param string[] $fake {@internal ne pas utiliser en PROD}
     */
    protected function extensionDetector(?array $fake = \null): void
    {
        if (empty($this->installedExtensions)) {
            $this->brands = [];
            $loaded =  $fake ?? \array_map('strtolower', \get_loaded_extensions());
            foreach($this->ALL_EXTENSIONS as $ext) {
                if (\in_array($ext, $loaded)) {
                    $brand = \array_filter(
                        $this->ALL_BRANDS,
                        fn($brand) => \str_contains($ext, $brand)
                    );
                    $brand = (string) \array_shift($brand);
                    if (!\in_array($brand, $this->brands)) {
                        $this->brands[] = $brand;
                        $this->installedExtensions[] = $ext;
                    }
                }
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param array<string,string>|null $fake
     */
    protected function iniParametersDetector(?array $fake = \null): void
    {
        $iniParameters = [
            'pdo_mysql.default_socket',
            'mysqli.default_socket',
            'mysqli.default_host',
            'mysqli.default_port',
            'mysqli.default_pw',
            'mysqli.default_user',
        ];
    }

    /**
     * Undocumented function
     *
     * @param array<string,string>|null $fake
     */
    protected function commonEnvVars(?array $fake = \null): void
    {
        $envVars = [
            'MYSQL_ROOT_PASSWORD',
            'MYSQL_DATABASE',
            'MYSQL_USER',
            'MYSQL_PASSWORD',
            'PGHOST',
            'PGDATABASE',
            'PGUSER',
            'PGPASSWORD'
        ];
    }
}
