<?php

namespace SchedulerTests\Action;

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

    /**
     * @return RRule
     */
    private function getRRule()
    {
        $startDate = new \DateTime('2013-06-12 20:00:00');
        return new RRule('FREQ=MONTHLY;COUNT=5', $startDate);
    }

}