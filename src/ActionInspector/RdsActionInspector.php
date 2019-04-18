<?php

namespace Scheduler\ActionInspector;

use Scheduler\Action\ActionInterface;
use Scheduler\Exception\SchedulerException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;

/**
 * Class RdsActionInspector
 * @package Scheduler\ActionInspector
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class RdsActionInspector extends AbstractActionInspector
{
    const TABLE_NAME = 'scheduler_action_inspector';
    const COLUMN_ID = 'id';
    const COLUMN_REPORT = 'report';
    const COLUMN_STATE = 'state';
    const COLUMN_CREATED_AT = 'created_at';

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /**
     * RdsActionInspector constructor.
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param ActionInterface $action
     * @return boolean returns `false` if action is already exists in this state in the log
     * @throws SchedulerException
     */
    public function update(ActionInterface $action)
    {
        $actionId = $action->getId();
        $actionState = $action->getState();
        $result = false;
        $previousState = null;
        try {
            $selectQb = $this->getSelectQuery($actionId);

            if ($dbResult = $selectQb->execute()->fetch(\PDO::FETCH_ASSOC)) {
                $previousState = $dbResult[self::COLUMN_STATE];
                $qb = $this->getUpdateQuery($actionState, $actionId);
            } else {
                $qb = $this->getInsertQuery($actionState, $actionId);
            }

            if ($actionState === ActionInterface::STATE_FINISHED) {
                $qb->set(self::COLUMN_REPORT, ':report')
                    ->setParameter(self::COLUMN_REPORT, $action->getReport());
            }

            if ($this->isStateAllowed($action, $previousState)) {
                $result = $qb->execute() === 1;
            }
        } catch (DBALException $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->getConnection()->createQueryBuilder();
    }

    /**
     * @param $actionId
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getSelectQuery($actionId)
    {
        return $this->getQueryBuilder()
            ->select(implode(',', [self::COLUMN_REPORT, self::COLUMN_STATE, self::COLUMN_ID]))
            ->from(self::TABLE_NAME)
            ->andWhere(self::COLUMN_ID . ' = ?')
            ->setParameter(0, $actionId);
    }

    /**
     * @param $actionState
     * @param $actionId
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getUpdateQuery($actionState, $actionId)
    {
        return $this->getQueryBuilder()
            ->update(self::TABLE_NAME)
            ->set(self::COLUMN_STATE, ':state')
            ->set(self::COLUMN_CREATED_AT, ':created_at')
            ->where(self::COLUMN_ID . ' = :id')
            ->setParameters([
                self::COLUMN_STATE => $actionState,
                self::COLUMN_ID => $actionId,
                self::COLUMN_CREATED_AT => (new \DateTime)->format('Y-m-d H:i:s')
            ]);
    }

    /**
     * @param $actionState
     * @param $actionId
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getInsertQuery($actionState, $actionId)
    {
        return $this->getQueryBuilder()
            ->insert(self::TABLE_NAME)
            ->values([
                self::COLUMN_ID => ':id',
                self::COLUMN_STATE => ':state',
                self::COLUMN_CREATED_AT => ':created_at',
            ])
            ->setParameters([
                self::COLUMN_ID => $actionId,
                self::COLUMN_STATE => $actionState,
                self::COLUMN_CREATED_AT => (new \DateTime)->format('Y-m-d H:i:s'),
            ]);
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function initDb(\Doctrine\DBAL\Connection $connection)
    {
        $schemaManager = $connection->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;
        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn(static::COLUMN_ID, TYPE::STRING, ['length' => 255, 'notnull' => true]);
        $table->addColumn(static::COLUMN_STATE, TYPE::STRING, ['length' => 255, 'notnull' => true]);
        $table->addColumn(static::COLUMN_REPORT, TYPE::TEXT, ['notnull' => false]);
        $table->addColumn(static::COLUMN_CREATED_AT, TYPE::DATETIME, ['notnull' => true]);
        $table->setPrimaryKey([static::COLUMN_ID]);
        $table->addIndex([static::COLUMN_ID], 'IDX_' . static::TABLE_NAME . '_' . static::COLUMN_ID);
        $queries = $fromSchema->getMigrateToSql($schema, $connection->getDatabasePlatform());
        foreach ($queries as $query) {
            $connection->exec($query);
        }
    }

    public static function dropDb(\Doctrine\DBAL\Connection $connection)
    {
        $schemaManager = $connection->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;
        $schema->dropTable(self::TABLE_NAME);
        $queries = $fromSchema->getMigrateToSql($schema, $connection->getDatabasePlatform());
        foreach ($queries as $query) {
            $connection->exec($query);
        }
    }
}