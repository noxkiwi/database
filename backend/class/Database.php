<?php declare(strict_types = 1);
namespace noxkiwi\database;

use Exception;
use noxkiwi\core\ErrorHandler;
use noxkiwi\core\Helper\JsonHelper;
use noxkiwi\database\Exception\DatabaseException;
use noxkiwi\database\Interfaces\DatabaseInterface;
use noxkiwi\database\Observer\DatabaseObserver;
use noxkiwi\log\Traits\LogTrait;
use noxkiwi\observing\Observable\ObservableInterface;
use noxkiwi\observing\Traits\ObservableTrait;
use noxkiwi\singleton\Singleton;
use PDO;
use PDOStatement;
use function compact;
use const E_ERROR;
use const E_USER_NOTICE;

/**
 * I am the base database class.
 *
 * @package      noxkiwi\database
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2020 - 2021 nox.kiwi
 * @version      1.0.2
 * @link         https://nox.kiwi/
 */
abstract class Database extends Singleton implements DatabaseInterface, ObservableInterface
{
    use LogTrait;
    use ObservableTrait;

    protected const USE_DRIVER = true;
    /** @var array I am a list of queries that have been run. */
    public static array $queries = [];
    /** @var string I am the last query that was called. */
    protected string $lastQuery;
    /** @var string Contains the current driver name */
    protected string $driver;
    /** @var \PDO Contains the \PDO instance of this DB instance */
    protected PDO $connection;
    /** @var \PDOStatement Contains the \PDOStatement instance of this DB instance after performing a query */
    protected PDOStatement $statement;

    /**
     * Creates an instance with the given $config
     *
     * @param array $config
     *
     * @throws \noxkiwi\database\Exception\DatabaseException
     */
    protected function __construct(array $config)
    {
        parent::__construct();
        $this->lastQuery = '';
        try {
            $this->connection = $this->getPdoConnection($config);
        } catch (Exception $exception) {
            ErrorHandler::handleException($exception, E_USER_NOTICE);
            throw new DatabaseException('EXCEPTION_CONSTRUCT_CONNECTION_ERROR', E_ERROR, $config);
        }
        $this->attach(new DatabaseObserver());
    }

    /**
     * I will create the connection to the Database using the given $config.
     *
     * @param array $config
     *
     * @throws \PDOException
     * @return \PDO
     */
    protected function getPdoConnection(array $config): PDO
    {
        if (empty($config['user']) || empty($config['pass'])) {
            return new PDO($this->getConnectionString($config));
        }

        return new PDO($this->getConnectionString($config), $config['user'], $config['pass']);
    }

    /**
     * I will return the ConnectionString for the PDO object.
     * The ConnectionString will contain the necessary $options.
     *
     * @param array $options
     *
     * @return string
     */
    abstract protected function getConnectionString(array $options): string;

    /**
     * @inheritDoc
     */
    final public function getResult(): array
    {
        return $this->statement->fetchAll();
    }

    /**
     * @inheritDoc
     */
    final public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * @inheritDoc
     * @throws \noxkiwi\database\Exception\DatabaseException
     */
    final public function read(string $query, array $data = null): void
    {
        $this->notify(DatabaseObserver::SELECT);
        $this->query($query, $data);
    }

    /**
     * Performs the given $queryString on the \PDO instance.
     * The given $queryData will be used through \PDO Data Handling.
     * <br />Stores the \PDOStatement to the instance for result management
     *
     * @param string     $string
     * @param array|null $data
     *
     * @throws \noxkiwi\database\Exception\DatabaseException
     */
    protected function query(string $string, array $data = null): void
    {
        /** @var array $data */
        $this->logDebug($string);
        $this->logDebug(JsonHelper::encode($data));
        $this->lastQuery   = $string;
        static::$queries[] = compact('string', 'data');
        $this->notify(DatabaseObserver::QUERY);
        $statement = $this->connection->prepare($string);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        if ($statement->execute($data) === false) {
            $info = ['errors' => $statement->errorInfo(), 'query' => $string, 'data' => $data];
            throw new DatabaseException('EXCEPTION_QUERY_ERROR', E_ERROR, $info);
        }
        $this->statement = $statement;
    }

    /**
     * @inheritDoc
     * @throws \noxkiwi\database\Exception\DatabaseException
     */
    final public function write(Query $query): void
    {
        $this->notify(DatabaseObserver::WRITE);
        $this->query($query->string, $query->data);
    }

    /**
     * @inheritDoc
     */
    public function beginTransaction(): void
    {
        if ($this->connection->inTransaction()) {
            return;
        }
        $this->connection->beginTransaction();
    }

    /**
     * @inheritDoc
     */
    public function commit(): void
    {
        if (! $this->connection->inTransaction()) {
            return;
        }
        $this->connection->commit();
    }

    /**
     * @inheritDoc
     */
    public function rollback(): void
    {
        if (! $this->connection->inTransaction()) {
            return;
        }
        $this->connection->rollBack();
    }

    /**
     * I will return the last query that was sent using this connection.
     * @return string
     */
    final public function getLastQuery(): string
    {
        return $this->lastQuery;
    }
}
