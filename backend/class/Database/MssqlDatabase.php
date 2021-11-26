<?php declare(strict_types = 1);
namespace noxkiwi\database\Database;

use noxkiwi\core\Exception\SystemComponentException;
use noxkiwi\database\Database;
use function extension_loaded;
use const E_ERROR;

/**
 * I am the Microsoft SQL Database class
 *
 * @package      noxkiwi\database\Database
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2019 - 2021 nox.kiwi
 * @version      1.0.1
 * @link         https://nox.kiwi/
 */
final class MssqlDatabase extends Database
{
    protected const DRIVER = 'mssql';

    /**
     * @inheritDoc
     * @throws \noxkiwi\core\Exception\SystemComponentException
     */
    protected function __construct(array $config)
    {
        if (! extension_loaded('pdo_sqlsrv')) {
            throw new SystemComponentException('MISSING_EXTENSION_PDO_MSSQL', E_ERROR);
        }
        $this->driver = 'mssql';
        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    protected function getConnectionString(array $options): string
    {
        return self::DRIVER . ':dbname=' . $options['database'] . ';host=' . $options['host'];
    }
}
