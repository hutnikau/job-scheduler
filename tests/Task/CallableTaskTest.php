<?php

namespace SchedulerTests\Task;

use PHPUnit\Framework\TestCase;
use Scheduler\Task\CallableTask;
use Recurr\Rule;

/**
 * Class CallableTaskTest
 * @package SchedulerTests\Task
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>\
 */
class CallableTaskTest extends TestCase
{
    public function testGerRRule()
    {
        $rule = $this->getRRule();
        $task = new CallableTask($rule, function (){});
        $this->assertEquals($rule, $task->getRRule());
    }

    public function testInvoke()
    {
        $callbackMock = $this->getMockBuilder('\stdClass')
            ->setMethods(['myCallBack'])
            ->getMock();

        $callbackMock->expects($this->once())
            ->method('myCallBack')
            ->will($this->returnValue(true));

        $rule = $this->getRRule();
        $task = new CallableTask($rule, [$callbackMock, 'myCallBack']);
        $this->assertTrue($task());
    }

    private function getRRule()
    {
        $startDate   = new \DateTime('2013-06-12 20:00:00');
        $endDate     = new \DateTime('2013-06-14 20:00:00'); // Optional
        return new Rule('FREQ=MONTHLY;COUNT=5', $startDate, $endDate);
    }
}
