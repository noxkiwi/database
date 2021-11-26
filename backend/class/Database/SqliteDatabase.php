<?php declare(strict_types = 1);
namespace noxkiwi\database\Database;

use noxkiwi\core\ErrorHandler;
use noxkiwi\core\Exception\SystemComponentException;
use noxkiwi\core\Helper\JsonHelper;
use noxkiwi\database\Database;
use noxkiwi\database\Exception\DatabaseException;
use PDO;
use PDOException;
use PDOStatement;
use function compact;
use function extension_loaded;
use const E_ERROR;

/**
 * I am the SQLite Database driver.
 *
 * @package      noxkiwi\database\Database
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2020 - 2021 nox.kiwi
 * @version      1.0.1
 * @link         https://nox.kiwi/
 */
final class SqliteDatabase extends Database
{
    /**
     * @inheritDoc
     *
     * @param array $config
     *
     * @throws \noxkiwi\core\Exception\SystemComponentException
     * @throws \noxkiwi\database\Exception\DatabaseException
     */
    protected function __construct(array $config)
    {
        if (! extension_loaded('pdo_sqlite')) {
            throw new SystemComponentException('MISSING_EXTENSION_PDO_SQLITE', E_ERROR);
        }
        parent::__construct($config);
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     * @inheritDoc
     */
    protected function query(string $string, array $data = null): void
    {
        /** @var array $data */
        $this->logDebug($string);
        $this->logDebug(JsonHelper::encode($data));
        self::$queries[] = compact('string', 'data');
        try {
            $statement = $this->connection->prepare($string);
        } catch (PDOException $exception) {
            ErrorHandler::handleException($exception);

            return;
        }
        if (! $statement instanceof PDOStatement) {
            $info = [
                'statement' => $statement,
                'query'     => $string,
                'data'      => $data,
                'EI'        => $this->connection->errorInfo(),
                'conn'      => $this->connection
            ];
            throw new DatabaseException('EXCEPTION_QUERY_ERROR', E_ERROR, $info);
        }
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        if ($statement->execute($data) === false) {
            $errorInfo = (int)$this->connection->errorInfo()[0];
            if (! empty($errorInfo)) {
                $info = [
                    'statement' => $statement,
                    'query'     => $string,
                    'data'      => $data,
                    'EI'        => $this->connection->errorInfo(),
                    'conn'      => $this->connection
                ];
                throw new DatabaseException('EXCEPTION_QUERY_ERROR', E_ERROR, $info);
            }
        }
        $this->statement = $statement;
        $this->notify();
    }

    /**
     * @inheritDoc
     */
    protected function getConnectionString(array $options): string
    {
        return 'sqlite:' . $options['file'];
    }
}
