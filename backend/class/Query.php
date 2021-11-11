<?php declare(strict_types = 1);
namespace noxkiwi\database;

use function array_merge;

/**
 * I am the Query object.
 *
 * @package      noxkiwi\database
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2021 nox.kiwi
 * @version      1.0.1
 * @link         https://nox.kiwi/
 */
final class Query
{
    /** @var string I am the query string to use. */
    public string $string = '';
    /** @var array I am the query data to use. */
    public array $data = [];

    /**
     * I will attach the given $addon to the Query object.
     *
     * @param \noxkiwi\database\QueryAddon $addon
     */
    public function attach(QueryAddon $addon): void
    {
        $this->string .= " $addon->string ";
        $this->data   = array_merge($this->data, $addon->data);
    }
}
