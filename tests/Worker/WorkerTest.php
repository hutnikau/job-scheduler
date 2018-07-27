<?php

namespace SchedulerTests\Worker;

use Scheduler\JobRunner\JobRunner;
use Scheduler\Worker\Worker;
use Scheduler\Scheduler;
use Scheduler\Job\RRule;
use Scheduler\Job\Job;
use PHPUnit\Framework\TestCase;

/**
 * Class WorkerTest
 * @package Scheduler\Worker
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class WorkerTest extends TestCase
{

    protected $reports;

    public function testRun()
    {
        $time = time();
        $this->reports = new \stdClass();
        $this->reports->arr = [];
        $worker = new Worker($this->getJobRunner(), $this->getScheduler(2));
        $worker->setMaxIterations(3);
        $worker->run($time, 'PT1S');
        $this->assertEquals(2, count($this->reports->arr));
        $this->assertFalse($worker->isRunning());
    }

    public function testRunAsync()
    {
        $samplesDir = __DIR__ . DIRECTORY_SEPARATOR . 'Samples' . DIRECTORY_SEPARATOR;
        $pipe = [];
        $start = time();
        $maxIterations = 1;
        $interval = 'PT1S';
        $threads = 3;
        require_once $samplesDir . 'jobs.php';
        /** @var $jobs */
        file_put_contents($samplesDir . 'report', '');

        for ($i = 0; $i < $threads; $i++) {
            $pipe[$i] = popen('php ' . $samplesDir . 'AsyncWorker.php ' . $start . ' ' . $maxIterations . ' ' . $interval, 'w');
        }

        for ($i = 0; $i < $threads; $i++) {
            pclose($pipe[$i]);
        }

        $performedActionIds = file($samplesDir . 'report');
        $this->assertEquals(count(array_unique($performedActionIds)), count($performedActionIds));
        $this->assertEquals($maxIterations * count($jobs), count($performedActionIds));
    }

    /**
     * @expectedException Scheduler\Exception\SchedulerException
     */
    public function testSetMaxIterationsException()
    {
        $worker = new Worker($this->getJobRunner(), new Scheduler());
        $worker->setMaxIterations('foo');
    }

    /**
     * @expectedException Scheduler\Exception\SchedulerException
     */
    public function testRunException()
    {
        $worker = new Worker($this->getJobRunner(), new Scheduler());
        $worker->run('foo', 'PT1S');
    }

    /**
     * @return JobRunner
     */
    protected function getJobRunner()
    {
        return new JobRunner();
    }

    /**
     * @param $numOfJobs
     * @return Scheduler
     */
    protected function getScheduler($numOfJobs)
    {
        $reports = $this->reports;
        $jobs = [];
        $startTime = time();
        for ($jubNum = 0; $numOfJobs > $jubNum; $jubNum++) {
            $callbackMock = $this->getMockBuilder('\stdClass')
                ->setMethods(['myCallBack'])
                ->getMock();
            $callbackMock->expects($this->once())
                ->method('myCallBack')
                ->will($this->returnCallback(function () use ($reports, $jubNum) {
                    sleep(1);
                    $reports->arr[] = $jubNum;
                    return $jubNum;
                }));
            $jobs[] = $this->getJob($startTime + $jubNum, 'FREQ=MONTHLY;COUNT=5', $callbackMock);
        }
        $scheduler = new Scheduler($jobs);

        return $scheduler;
    }

    /**
     * @param integer $start
     * @param string $rrule
     * @param callback $callbackMock
     * @return Job
     */
    private function getJob($start, $rrule = 'FREQ=MONTHLY;COUNT=5', $callbackMock)
    {
        $timezone = 'UTC';
        $startDate = \DateTime::createFromFormat('U', $start, new \DateTimeZone($timezone));
        $rule = new RRule($rrule, $startDate);
        return new Job($rule, [$callbackMock, 'myCallBack']);
    }
}