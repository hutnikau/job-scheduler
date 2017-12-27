<?php

namespace SchedulerTests\JobRunner;

use PHPUnit\Framework\TestCase;
use Scheduler\Scheduler;
use Scheduler\Job\CallableAction;
use Recurr\Rule;
use DateTime;
use Scheduler\JobRunner\JobRunner;

/**
 * Class TaskRunnerTest
 * @package SchedulerTests\TaskRunner
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class jobRunnerTest extends TestCase
{
    public function testRun()
    {
        $now = time();
        $scheduler = new Scheduler([
            $this->getTask($now-5),
            $this->getTask($now),
        ]);
        $taskRunner = new JobRunner($scheduler);
        $taskRunner->run();
    }


    /**
     * @return CallableAction
     */
    private function getTask($start, $rrule = 'FREQ=MONTHLY;COUNT=5')
    {
        $callbackMock = $this->getMockBuilder('\stdClass')
            ->setMethods(['myCallBack'])
            ->getMock();

        $callbackMock->expects($this->once())
            ->method('myCallBack')
            ->will($this->returnValue(true));

        $timezone = 'UTC';
        $startDate = DateTime::createFromFormat('U', $start, new \DateTimeZone($timezone));
        $rule = new Rule($rrule, $startDate, null, $timezone);
        return new CallableAction($rule, [$callbackMock, 'myCallBack']);
    }
}