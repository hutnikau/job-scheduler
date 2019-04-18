<?php

namespace SchedulerTests\ActionLog;

use Scheduler\Action\ActionInterface;
use Scheduler\Action\CallableAction;
use Scheduler\ActionInspector\RdsActionInspector;
use PHPUnit\Framework\TestCase;
use Scheduler\Job\Job;
use Scheduler\Job\RRule;
use Doctrine\DBAL\DBALException;

/**
 * Class RdsActionInspectorTest
 * @package SchedulerTests\ActionInspector
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class RdsActionInspectorTest extends TestCase
{
    public function testUpdate()
    {
        $job = new Job($this->getRRule(), function () {
            return 'report';
        });
        $action = new CallableAction($job, new \DateTime('2018-06-12 20:00:00'));
        $action2 = new CallableAction($job, new \DateTime('2018-06-12 20:01:00'));
        $action3 = new CallableAction($job, new \DateTime('2018-06-12 20:02:00'));

        $connection = $this->getConnection();
        RdsActionInspector::initDb($connection);
        $actionLog = new RdsActionInspector($connection);

        $this->assertTrue($actionLog->update($action));
        $this->assertFalse($actionLog->update($action));

        $action();

        $this->assertTrue($actionLog->update($action));
        $this->assertFalse($actionLog->update($action));

        $this->assertTrue($actionLog->update($action2));
        $this->assertFalse($actionLog->update($action2));

        $this->assertFalse($actionLog->update($action));

        //$action3 is in initial state
        $this->assertTrue($actionLog->update($action3));
        //$action3 is in in finished state
        $action3();
        $this->assertTrue($actionLog->update($action3));

        $refAction = new \ReflectionObject($action3);
        $actionStateProperty = $refAction->getProperty('state');
        $actionStateProperty->setAccessible(true);
        $actionStateProperty->setValue($action3, ActionInterface::STATE_IN_PROGRESS);
        $this->assertEquals(ActionInterface::STATE_IN_PROGRESS, $action3->getState());

        //attempt to return back from finished state to in progress
        $this->assertFalse($actionLog->update($action3));

        $reports = $connection->query('SELECT * FROM ' . RdsActionInspector::TABLE_NAME)->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertCount(count($reports), array_filter(array_column($reports, RdsActionInspector::COLUMN_CREATED_AT)));

        //in case of DBALException (such as index violation) is should return false
        RdsActionInspector::dropDb($connection);
        $this->assertFalse($actionLog->update($action3));
    }

    public function testDropDb()
    {
        $connection = $this->getConnection();
        RdsActionInspector::initDb($connection);
        $this->assertEquals(RdsActionInspector::TABLE_NAME, $connection->getSchemaManager()->listTables()[0]->getName());
        RdsActionInspector::dropDb($connection);
        $this->assertEquals(0, count($connection->getSchemaManager()->listTables()));
    }

    public function testConcurrencyUpdate()
    {
        $samplesDir = __DIR__ . DIRECTORY_SEPARATOR . 'Samples' . DIRECTORY_SEPARATOR;
        $connectionParams = array(
            'url' => 'sqlite:///' . $samplesDir . 'db.sqlite',
            'driver' => 'pdo_sqlite',
        );

        $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, new \Doctrine\DBAL\Configuration());

        try {
            RdsActionInspector::dropDb($connection);
        } catch (DBALException $e) {
            //table does not exist
        }

        RdsActionInspector::initDb($connection);

        $pipe = [];
        $threads = 100;
        $start = time();
        /** @var $actions */
        require_once $samplesDir . 'actions.php';

        for ($i = 0; $i < $threads; $i++) {
            $pipe[$i] = popen('php ' . $samplesDir . 'AsyncUpdate.php ' . $i . ' ' . $start, 'w');
        }

        for ($i = 0; $i < $threads; $i++) {
            pclose($pipe[$i]);
        }

        $actionReports = $connection->query('SELECT * FROM ' . RdsActionInspector::TABLE_NAME)->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertEquals(count($actions), count($actionReports));
        foreach ($actionReports as $actionReport) {
            $this->assertEquals(ActionInterface::STATE_FINISHED, $actionReport[RdsActionInspector::COLUMN_STATE]);
            $this->assertEquals('success', $actionReport[RdsActionInspector::COLUMN_REPORT]);
        }

        $actionReportIds = array_column($actionReports, RdsActionInspector::COLUMN_ID);

        foreach ($actions as $action) {
            $this->assertTrue(in_array($action->getId(), $actionReportIds));
        }
    }

    /**
     * @return \Doctrine\DBAL\Connection
     * @throws DBALException
     */
    private function getConnection()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('sqlite not found, tests skipped.');
        }
        $connectionParams = array(
            'url' => 'sqlite:///:memory:',
            'driver' => 'pdo_sqlite',
        );
        return \Doctrine\DBAL\DriverManager::getConnection($connectionParams, new \Doctrine\DBAL\Configuration());
    }

    /**
     * @return RRule
     */
    private function getRRule()
    {
        $startDate = new \DateTime('2013-06-12 20:00:00');
        return new RRule('FREQ=MONTHLY;COUNT=5', $startDate);
    }
}