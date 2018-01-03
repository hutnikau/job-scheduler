<?php

namespace SchedulerTests;

use PHPUnit\Framework\TestCase;
use Scheduler\Scheduler;
use Scheduler\Action\CallableAction;
use Scheduler\Job\Job;
use Scheduler\Job\RRule;
use DateTime;

/**
 * Class SchedulerTest
 * @package SchedulerTests\Task
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class SchedulerTest extends TestCase
{
    /**
     * @dataProvider getIteratorProvider
     */
    public function testGetIterator($params, $tasks, $expected)
    {
        $scheduler = new Scheduler($tasks);

        //all tasks should be returned as start time is before the earliest task
        $iterator = call_user_func_array([$scheduler, 'getIterator'], $params);
        $this->assertTrue($iterator instanceof \Iterator);
        $resultArray = iterator_to_array($iterator, false);
        $this->assertEquals($expected, $resultArray);
    }

    public function testAddTask()
    {
        $time = time();
        $scheduler = new Scheduler([]);
        $iterator = $scheduler->getIterator(DateTime::createFromFormat('U', $time-2));
        $resultArray = iterator_to_array($iterator);
        $this->assertTrue(empty($resultArray));

        $scheduler->addJob($this->getJob(DateTime::createFromFormat('U', $time-1)));

        $iterator = $scheduler->getIterator(DateTime::createFromFormat('U', $time-2));
        $resultArray = iterator_to_array($iterator);
        $this->assertEquals(count($resultArray), 1);
    }

    public function getIteratorProvider()
    {
        $time = time();
        $timezone = 'UTC';
        $times = [
            DateTime::createFromFormat('U', $time-10, new \DateTimeZone($timezone)),
            DateTime::createFromFormat('U', $time-1, new \DateTimeZone($timezone)),
            DateTime::createFromFormat('U', $time, new \DateTimeZone($timezone)),
            DateTime::createFromFormat('U', $time+10, new \DateTimeZone($timezone))
        ];
        $jobs = [
            $this->getJob($times[0]),
            $this->getJob($times[1]),
            $this->getJob($times[2]),
            $this->getJob($times[3]),
        ];
        return [
            [
                [DateTime::createFromFormat('U', $time-10), DateTime::createFromFormat('U', $time+11), true],
                $jobs,
                [
                    new CallableAction($jobs[0], $times[0]),
                    new CallableAction($jobs[1], $times[1]),
                    new CallableAction($jobs[2], $times[2]),
                    new CallableAction($jobs[3], $times[3]),
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time-10), DateTime::createFromFormat('U', $time+11), false],
                $jobs,
                [
                    new CallableAction($jobs[1], $times[1]),
                    new CallableAction($jobs[2], $times[2]),
                    new CallableAction($jobs[3], $times[3]),
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time-10), DateTime::createFromFormat('U', $time), true],
                $jobs,
                [
                    new CallableAction($jobs[0], $times[0]),
                    new CallableAction($jobs[1], $times[1]),
                    new CallableAction($jobs[2], $times[2]),
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time-10), DateTime::createFromFormat('U', $time), false],
                $jobs,
                [
                    new CallableAction($jobs[1], $times[1]),
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time), DateTime::createFromFormat('U', $time), true],
                $jobs,
                [
                    new CallableAction($jobs[2], $times[2]),
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time+5), DateTime::createFromFormat('U', $time+10), true],
                $jobs,
                [
                    new CallableAction($jobs[3], $times[3]),
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time+11), DateTime::createFromFormat('U', $time), true],
                $jobs,
                []
            ],
            [
                [DateTime::createFromFormat('U', $time-11), DateTime::createFromFormat('U', $time-9)],
                $jobs,
                [
                    new CallableAction($jobs[0], $times[0]),
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time-2), DateTime::createFromFormat('U', $time), true],
                [$secondlyJob = $this->getJob(DateTime::createFromFormat('U', $time-2), 'FREQ=SECONDLY;COUNT=5')],
                [
                    new CallableAction($secondlyJob, DateTime::createFromFormat('U', $time-2)),
                    new CallableAction($secondlyJob, DateTime::createFromFormat('U', $time-1)),
                    new CallableAction($secondlyJob, DateTime::createFromFormat('U', $time)),
                ]
            ],
            //check if actions ordered by start time
            [
                [DateTime::createFromFormat('U', $time-2), DateTime::createFromFormat('U', $time), true],
                [
                    $thirdJob = $this->getJob(DateTime::createFromFormat('U', $time)),
                    $firstJob = $this->getJob(DateTime::createFromFormat('U', $time-1)),
                    $secondJob = $this->getJob(DateTime::createFromFormat('U', $time-1)),
                ],
                [
                    new CallableAction($firstJob, DateTime::createFromFormat('U', $time-1)),
                    new CallableAction($secondJob, DateTime::createFromFormat('U', $time-1)),
                    new CallableAction($thirdJob, DateTime::createFromFormat('U', $time)),
                ]
            ],
        ];
    }

    /**
     * @return Job
     */
    private function getJob(\DateTimeInterface $startDate, $rrule = 'FREQ=MONTHLY;COUNT=5')
    {
        $callbackMock = $this->getMockBuilder('\stdClass')
            ->setMethods(['myCallBack'])
            ->getMock();

        $rule = new RRule($rrule, $startDate);
        return new Job($rule, [$callbackMock, 'myCallBack']);
    }
}