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
    public function getIterator(DateTimeInterface $from, DateTimeInterface $to = null, $inc = true)
    {
        $iterator = new \ArrayIterator();
        if ($to === null) {
            $to = new \DateTime('now', $from->getTimezone());
        }
        foreach ($this->jobs as $job) {
            $this->appendActions($iterator, $job, $from, $to, $inc);
        }
        $this->sortActions($iterator);
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

    /**
     * @param \ArrayIterator $iterator - iterator to append actions
     * @param JobInterface $job
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param $inc
     */
    protected function appendActions(\ArrayIterator $iterator, JobInterface $job, DateTimeInterface $from, DateTimeInterface $to, $inc)
    {
        $actionIterator = new ActionIterator($job, $from, $to, $inc);
        foreach ($actionIterator as $action) {
            $iterator->append($action);
        }
    }

    /**
     * Sort actions by execution time
     * @param \ArrayIterator $iterator - iterator to append actions
     */
    protected function sortActions(\ArrayIterator $iterator)
    {
        $iterator->uasort(function ($a, $b) {
            return $a->getTime()->getTimestamp() - $b->getTime()->getTimestamp();
        });
    }
}
