<?php declare(strict_types = 1);
namespace noxkiwi\database;

/**
 * I am the Query Addon object.
 *
 * @package      noxkiwi\database
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2021 nox.kiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class QueryAddon
{
    /** @var string I am the query string to use. */
    public string $string = '';
    /** @var array I am the query data to use. */
    public array $data = [];
}
