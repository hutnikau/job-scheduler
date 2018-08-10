<?php

namespace SchedulerTests\ActionLog;

use Scheduler\Action\ActionInterface;
use Scheduler\Action\CallableAction;
use Scheduler\ActionInspector\FileActionInspector;
use PHPUnit\Framework\TestCase;
use Scheduler\Job\Job;
use Scheduler\Job\RRule;

/**
 * Class FileActionInspectorTest
 * @package SchedulerTests\ActionInspector
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class FileActionInspectorTest extends TestCase
{
    public function testUpdate()
    {
        $job = new Job($this->getRRule(), function () {
            return 'report';
        });
        $filename = __DIR__.DIRECTORY_SEPARATOR.'actions.log';
        $action = new CallableAction($job, new \DateTime('2018-06-12 20:00:00'));
        $action2 = new CallableAction($job, new \DateTime('2018-06-12 20:01:00'));
        $action3 = new CallableAction($job, new \DateTime('2018-06-12 20:02:00'));

        if (file_exists($filename)) {
            unlink($filename);
        }
        $actionLog = new FileActionInspector($filename);

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

        $refAction   = new \ReflectionObject($action3);
        $actionStateProperty = $refAction->getProperty('state');
        $actionStateProperty->setAccessible(true);
        $actionStateProperty->setValue($action3, ActionInterface::STATE_IN_PROGRESS);
        $this->assertEquals(ActionInterface::STATE_IN_PROGRESS, $action3->getState());

        //attempt to return back from finished state to in progress
        $this->assertFalse($actionLog->update($action3));
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