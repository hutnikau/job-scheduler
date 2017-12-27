<?php

namespace Scheduler;

use Scheduler\Action\ActionIterator;
use Scheduler\Job\JobInterface;
use Scheduler\Job\JobIterator;
use DateTimeInterface;

/**
 * Class Scheduler
 * @package Scheduler
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class Scheduler implements SchedulerInterface
{
    /**
     * @var JobInterface[]
     */
    protected $jobs = [];

    /**
     * Scheduler constructor.
     * @param JobInterface[] $jobs
     */
    public function __construct(array $jobs = [])
    {
        foreach ($jobs as $job) {
            $this->addJob($job);
        }
    }

    /**
     * @inheritdoc
     */
    public function getIterator(DateTimeInterface $from, DateTimeInterface $to = null, bool $inc = true): \Iterator
    {
        $iterator = new \ArrayIterator();
        if ($to === null) {
            $to = new \DateTime('now', $from->getTimezone());
        }
        foreach ($this->jobs as $job) {
            $actionIterator = new ActionIterator($job, $from, $to, $inc);
            foreach ($actionIterator as $action) {
                $iterator->append($action);
            }
        }
        $iterator->uasort(function ($a, $b) {
            if ($a->getTime()->getTimestamp() == $b->getTime()->getTimestamp()) {
                return 0;
            }
            return ($a->getTime()->getTimestamp() < $b->getTime()->getTimestamp()) ? -1 : 1;
        });
        return $iterator;
    }

    /**
     * @param JobInterface $job
     * @return mixed|void
     */
    public function addJob(JobInterface $job)
    {
        $this->jobs[] = $job;
    }
}