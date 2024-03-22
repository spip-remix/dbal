<?php

declare(strict_types=1);

namespace SpipRemix\Component\Dbal;

/**
 * Undocumented interface
 *
 * @author JamesRezo <james@rezo.net>
 */
interface FactoryInterface
{
    /**
     * Undocumented function.
     *
     * @param non-empty-string ...$parameters
     *
     * @return SchemaInterface
     */
    public function createSchema(string ...$parameters): SchemaInterface;

    /**
     * Undocumented function.
     *
     * @param non-empty-string ...$parameters
     *
     * @return TableInterface
     */
    public function createTable(string ...$parameters): TableInterface;

    /**
     * Undocumented function
     *
     * @param string|boolean|null ...$parameters
     *
     * @return FieldInterface
     */
    public function createField(string|bool|null ...$parameters): FieldInterface;
}
