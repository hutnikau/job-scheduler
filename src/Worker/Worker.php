<?php

namespace Scheduler\Worker;

use Scheduler\JobRunner\JobRunnerInterface;
use Scheduler\SchedulerInterface;

/**
 * Class Worker
 * @package Scheduler\Worker
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class Worker extends AbstractWorker
{

    /** @var JobRunnerInterface */
    private $jobRunner;

    /** @var SchedulerInterface */
    private $scheduler;

    /**
     * Worker constructor.
     * @param JobRunnerInterface $jobRunner
     * @param SchedulerInterface $scheduler
     */
    public function __construct(JobRunnerInterface $jobRunner, SchedulerInterface $scheduler)
    {
        $this->jobRunner = $jobRunner;
        $this->scheduler = $scheduler;
    }

    /**
     * @return JobRunnerInterface
     */
    protected function getJobRunner()
    {
        return $this->jobRunner;
    }

    /**
     * @return SchedulerInterface
     */
    protected function getScheduler()
    {
        return $this->scheduler;
    }

}