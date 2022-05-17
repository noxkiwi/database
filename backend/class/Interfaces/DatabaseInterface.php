<?php declare(strict_types = 1);
namespace noxkiwi\database\Interfaces;

use noxkiwi\database\Query;

/**
 * I am the interface for all database drivers.
 *
 * @package      noxkiwi\database\Interfaces
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2018 - 2021 nox.kiwi
 * @version      1.0.2
 * @link         https://nox.kiwi/
 */
interface DatabaseInterface
{
    /**
     * I will return the array of records in the result.
     *
     * @return       array
     */
    public function getResult(): array;

    /**
     * I will return the last insterted ID for the past query
     *
     * @return       string
     */
    public function lastInsertId(): string;

    /**
     * I will perform a read query.
     *
     * @param string     $query
     * @param array|null $data
     */
    public function read(string $query, array $data = null): void;

    /**
     * I will perform a write query.
     *
     * @param \noxkiwi\database\Query $query
     */
    public function write(Query $query): void;

    /**
     * I will begin a transaction.
     *
     * @throws \noxkiwi\database\Exception\DatabaseException In case a transaction has already been started.
     */
    public function beginTransaction(): void;

    /**
     * I will commit a transaction.
     *
     * @throws \noxkiwi\database\Exception\DatabaseException In case no transaction was started prior.
     */
    public function commit(): void;

    /**
     * I will rollback the transaction.
     */
    public function rollback(): void;
}
