<?php

namespace SchedulerTests\Action;

use Scheduler\Action\ActionInterface;
use Scheduler\Action\CallableAction;
use PHPUnit\Framework\TestCase;
use Scheduler\Job\Job;
use Scheduler\Job\RRule;

/**
 * Class CallableActionTest
 * @package SchedulerTests\Action
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class CallableActionTest extends TestCase
{

    public function testGetTime()
    {
        $callbackMock = $this->getMockBuilder('\stdClass')
            ->setMethods(['myCallBack'])
            ->getMock();
        $job = new Job($this->getRRule(), [$callbackMock, 'myCallBack']);
        $time = new \DateTime('2013-06-12 20:00:00');
        $action = new CallableAction($job, $time);
        $this->assertEquals($time, $action->getTime());
    }

    public function testGetJob()
    {
        $callbackMock = $this->getMockBuilder('\stdClass')
            ->setMethods(['myCallBack'])
            ->getMock();
        $job = new Job($this->getRRule(), [$callbackMock, 'myCallBack']);
        $time = new \DateTime('2013-06-12 20:00:00');
        $action = new CallableAction($job, $time);
        $this->assertEquals($job, $action->getJob());
    }

    public function testInvoke()
    {
        $callbackMock = $this->getMockBuilder('\stdClass')
            ->setMethods(['myCallBack'])
            ->getMock();

        $callbackMock->expects($this->once())
            ->method('myCallBack')
            ->will($this->returnValue(true));
        $job = new Job($this->getRRule(), [$callbackMock, 'myCallBack']);
        $time = new \DateTime('2013-06-12 20:00:00');

        $action = new CallableAction($job, $time);
        $this->assertTrue($action());
    }

    public function testGetId()
    {
        $time = new \DateTime('2013-06-12 20:00:00');
        $rule = new RRule('FREQ=MINUTELY;COUNT=5', $time);
        $jobs = [
            new Job($rule, function () {}),
            new Job($rule, '\SchedulerTests\Action\my_callback_function'),
            new Job($rule, [new ActionClass(), 'myCallbackMethod']),
            new Job($rule, ['\SchedulerTests\Action\ActionClass', 'myCallbackMethod']),
            new Job($rule, new ActionClass()),
        ];
        $ids = [];
        foreach ($jobs as $job) {
            $action = new CallableAction($job, $time);
            $ids[] = $action->getId();
        }
        $this->assertEquals(count($jobs), count(array_unique($ids)));
    }

    public function testGetState()
    {
        $job = new Job($this->getRRule(), function ($action) {
            return $action->getState();
        });
        $action = new CallableAction($job, new \DateTime('2018-06-12 20:00:00'));

        $this->assertEquals(ActionInterface::STATE_INITIAL, $action->getState());
        $stateDuringExecution = $action();
        $this->assertEquals(ActionInterface::STATE_IN_PROGRESS, $stateDuringExecution);
        $this->assertEquals(ActionInterface::STATE_FINISHED, $action->getState());
    }

    public function testGetReport()
    {
        $job = new Job($this->getRRule(), function ($action) {
            return 'foo';
        });
        $action = new CallableAction($job, new \DateTime('2018-06-12 20:00:00'));
        $action();
        $this->assertEquals('foo', $action->getReport());
    }

    /**
     * @expectedException Scheduler\Exception\SchedulerException
     */
    public function testGetReportException()
    {
        $job = new Job($this->getRRule(), function ($action) {
            return $action->getState();
        });
        $action = new CallableAction($job, new \DateTime('2018-06-12 20:00:00'));
        $action->getReport();
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
function my_callback_function() {}
class ActionClass {
    static function myCallbackMethod() {}
    public function __invoke() {}
}