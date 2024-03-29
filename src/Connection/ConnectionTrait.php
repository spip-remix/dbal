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

trait ConnectionTrait
{
    public const ALL_BRANDS = ['mysql', 'sqlite', 'pgsql', ];

    public const ALL_EXTENSIONS = [
        // \PDO drivers
        'pdo_mysql', 'pdo_sqlite', 'pdo_pgsql',
        // vendor drivers
        'mysqli', 'mysqlnd', 'pgsql', 'sqlite3',
    ];

    /** @var string[] */
    protected array $brands = [];

    /** @var string[] */
    protected array $instaledExtensions = [];

    /**
     * @internal Pour les T.U. : Tricher sur les extensions présentes dans PHP.
     *
     * @param string[] $fake {@internal ne pas utiliser en PROD}
     */
    protected function extensionDetector(?array $fake = \null): void
    {
        if (empty($instaledExtensions)) {
            $this->brands = [];
            $loaded =  $fake ?? \array_map('strtolower', \get_loaded_extensions());
            foreach(self::ALL_EXTENSIONS as $ext) {
                if (\in_array($ext, $loaded)) {
                    $brand = \array_filter(
                        self::ALL_BRANDS,
                        fn($brand) => \str_contains($ext, $brand)
                    );
                    $brand = \array_shift($brand);
                    if (!\in_array($brand, $this->brands)) {
                        $this->brands[] = $brand;
                        $this->instaledExtensions[] = $ext;
                    }
                }
            }
        }
    }
}
