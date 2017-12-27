<?php

namespace SchedulerTests\Job;

use PHPUnit\Framework\TestCase;
use Scheduler\Job\Job;
use Recurr\Rule;

/**
 * Class JobTest
 * @package SchedulerTests\Job
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class JobTest extends TestCase
{
    public function testGetRRule()
    {
        $rule = $this->getRRule();
        $callbackMock = $this->getMockBuilder('\stdClass')
            ->setMethods(['myCallBack'])
            ->getMock();
        $task = new Job($rule, [$callbackMock, 'myCallBack']);
        $this->assertEquals($rule, $task->getRRule());
    }

    public function testGetCallable()
    {
        $rule = $this->getRRule();
        $callbackMock = $this->getMockBuilder('\stdClass')
            ->setMethods(['myCallBack'])
            ->getMock();
        $task = new Job($rule, [$callbackMock, 'myCallBack']);
        $this->assertEquals($rule, $task->getRRule());
    }

    /**
     * @return Rule
     */
    private function getRRule()
    {
        $startDate   = new \DateTime('2013-06-12 20:00:00');
        $endDate     = new \DateTime('2013-06-14 20:00:00'); // Optional
        return new Rule('FREQ=MONTHLY;COUNT=5', $startDate, $endDate);
    }

}
