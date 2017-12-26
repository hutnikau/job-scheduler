<?php

namespace SchedulerTests\Task;

use PHPUnit\Framework\TestCase;
use Scheduler\Scheduler;
use Scheduler\Task\CallableTask;
use Recurr\Rule;
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
        $resultArray = iterator_to_array($iterator);
        $this->assertEquals($expected, $resultArray);
    }

    public function testAddTask()
    {
        $time = time();
        $scheduler = new Scheduler([]);
        $iterator = $scheduler->getIterator(DateTime::createFromFormat('U', $time - 2));
        $resultArray = iterator_to_array($iterator);
        $this->assertTrue(empty($resultArray));

        $scheduler->addTask($this->getTask($time - 1));

        $iterator = $scheduler->getIterator(DateTime::createFromFormat('U', $time - 2));
        $resultArray = iterator_to_array($iterator);
        $this->assertEquals(count($resultArray), 1);
    }

    public function getIteratorProvider()
    {
        $time = time();
        $tasks = [
            $this->getTask($time-10),
            $this->getTask($time-1),
            $this->getTask($time),
            $this->getTask($time+10),
        ];
        return [
            [
                [DateTime::createFromFormat('U', $time-11), null],
                $tasks,
                $tasks
            ],
            [
                [DateTime::createFromFormat('U', $time-10), null, true],
                $tasks,
                $tasks
            ],
            [
                [DateTime::createFromFormat('U', $time-10), null, false],
                $tasks,
                [
                    $tasks[1],
                    $tasks[2],
                    $tasks[3],
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time), null, true],
                $tasks,
                [
                    $tasks[2],
                    $tasks[3],
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time+5), null, true],
                $tasks,
                [
                    $tasks[3],
                ]
            ],
            [
                [DateTime::createFromFormat('U', $time+11), null, true],
                $tasks,
                []
            ],
            [
                [DateTime::createFromFormat('U', $time-11), DateTime::createFromFormat('U', $time-9)],
                $tasks,
                [
                    $tasks[0]
                ]
            ],
        ];
    }

    /**
     * @return CallableTask
     */
    private function getTask($start)
    {
        $callbackMock = $this->getMockBuilder('\stdClass')
            ->setMethods(['myCallBack'])
            ->getMock();

        $callbackMock->expects($this->once())
            ->method('myCallBack')
            ->will($this->returnValue(true));

        $timezone = 'UTC';
        $startDate = DateTime::createFromFormat('U', $start, new \DateTimeZone($timezone));
        $rule = new Rule('FREQ=MONTHLY;COUNT=5', $startDate, null, $timezone);
        return new CallableTask($rule, [$callbackMock, 'myCallBack']);
    }
}