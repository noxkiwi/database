<?php declare(strict_types = 1);
namespace noxkiwi\database;

/**
 * I am the collection of hooks for the Database system.
 *
 * @package      noxkiwi\database
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2018 - 2021 nox.kiwi
 * @version      1.0.1
 * @link         https://nox.kiwi/
 */
final class Hook extends \noxkiwi\hook\Hook
{
    public const QUERY_BEFORE = 'query_success';
    public const QUERY_AFTER  = 'query_after';
    public const WRITE_BEFORE = 'write_before';
}
