<?php

namespace SchedulerTests\Job;

use PHPUnit\Framework\TestCase;
use Scheduler\Job\Job;
use Recurr\Rule;
use DateTime;
use DateTimeZone;

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

    public function testCreateFromString()
    {
        //createFromString($rRule, DateTimeInterface $startDate, callable $callback, $timezone = null)
        $dtString = '2017-12-28T21:00:00';
        $tzString = 'Europe/Minsk';
        $callback = function () {};

        //from strings
        $job = Job::createFromString('FREQ=MONTHLY;COUNT=5', $dtString, $callback, 'Europe/Minsk');
        $this->assertEquals('MONTHLY', $job->getRRule()->getFreqAsText());
        $this->assertEquals(5, $job->getRRule()->getCount());
        $this->assertEquals((new DateTime($dtString, new DateTimeZone($tzString)))->getTimestamp(), $job->getRRule()->getStartDate()->getTimestamp());
        $this->assertEquals('Europe/Minsk', $job->getRRule()->getStartDate()->getTimezone()->getName());

        //from instances
        $job = Job::createFromString('FREQ=MONTHLY;COUNT=5', new DateTime($dtString), $callback, null);
        $this->assertEquals((new DateTime($dtString))->getTimestamp(), $job->getRRule()->getStartDate()->getTimestamp());
        $this->assertEquals(date_default_timezone_get(), $job->getRRule()->getStartDate()->getTimezone()->getName());
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
