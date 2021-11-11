<?php declare(strict_types = 1);
namespace noxkiwi\database\Observer;

use noxkiwi\database\Database;
use noxkiwi\observing\Observable\ObservableInterface;
use noxkiwi\observing\Observer;

/**
 * I am the database observer.
 * I observe the database object and all its critical actions:
 *
 *   SELECT
 *     Automatically triggers READ.
 *     Prepends a QUERY observation.
 *
 *   INSERT
 *     Automatically triggers  WRITE observation.
 *     Prepends a QUERY observation.
 *
 *   UPDATE
 *     Automatically triggers  WRITE observation.
 *     Prepends a QUERY observation.
 *
 *   DELETE
 *     Automatically triggers  WRITE observation.
 *     Prepends a QUERY observation.
 *
 *   WRITE
 *     Acts as sum of WRITING queries.
 *
 *   READ
 *     Acts as sum of READING queries.
 *
 *   QUERY
 *     Only triggered, when a query is executed.
 *
 * For example, if you run a SELECT query >$database->read($query) would trigger:
 *  - READ     (automatically counts as READING query)
 *  - QUERY    (A real SQL query will be executed, the query will be logged.)
 *
 *
 * @package      noxkiwi\database\Observer
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2018 - 2021 nox.kiwi
 * @version      1.0.1
 * @link         https://nox.kiwi/
 */
class DatabaseObserver extends Observer
{
    public const SELECT = 'select';
    public const INSERT = 'insert';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const QUERY  = 'query';
    public const WRITE  = 'write';
    public const READ   = 'read';
    /** @var int Contains the count of queries that have been executed during runtime. */
    public static int      $queryCount = 0;
    /** @var array I am the list of queries that were executed during the runtime. */
    public static array    $queryList = [];
    /** @var int I am the amount of SELECT calls. */
    public static int      $selectCount = 0;
    /** @var int I am the amount of INSERT queries. */
    public static int      $insertCount = 0;
    /** @var int I am the amount of UPDATE queries. */
    public static int      $updateCount = 0;
    /** @var int I am the amount of DELETE queries. */
    public static int      $deleteCount = 0;
    /** @var int I am the amount of WRITE queries. */
    public static int     $writeCount = 0;
    /** @var int I am the amount of READ queries. */
    public static int     $readCount = 0;

    /**
     * @inheritDoc
     */
    public function update(ObservableInterface $observable, string $type): void
    {
        if (! $observable instanceof Database) {
            return;
        }
        switch ($type) {
            case self::SELECT:
                static::$selectCount++;
                static::$readCount++;
                break;
            case self::INSERT:
                static::$insertCount++;
                static::$writeCount++;
                break;
            case self::UPDATE:
                static::$updateCount++;
                static::$writeCount++;
                break;
            case self::DELETE:
                static::$deleteCount++;
                static::$writeCount++;
                break;
            case self::WRITE:
                static::$writeCount++;
                break;
            case self::QUERY:
                static::$queryCount++;
                static::$queryList[] = $observable->getLastQuery();
                break;
            default:
                break;
        }
    }
}
