<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal\Test\Fixtures;

use SpipRemix\Component\Dbal\Connection\ConnectionTrait;

class FakeDetector
{
    use ConnectionTrait;

    public function __construct($extensions)
    {
        $this->extensionDetector($extensions);
    }

    public function get(): mixed
    {
        return [
            'brands' => $this->brands,
            'installed' => $this->installedExtensions,
        ];
    }
}
