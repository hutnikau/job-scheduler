<?php

namespace Scheduler\Action;

use Recurr\Recurrence;
use DateTimeInterface;
use Scheduler\Job\JobInterface;

/**
 * Class ActionIterator
 * @package Scheduler
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class ActionIterator extends \ArrayIterator
{

    /**
     * RRuleIterator constructor.
     * @param JobInterface $job
     * @param DateTimeInterface $after
     * @param DateTimeInterface $before
     * @param bool $inc
     * @throws
     */
    public function __construct(JobInterface $job, DateTimeInterface $after, DateTimeInterface $before, $inc = true)
    {
        $dates = $job->getRRule()->getRecurrences($after, $before, $inc);
        $actions = [];
        /** @var Recurrence $recurrence */
        foreach ($dates as $recurrence) {
            $actions[] = new CallableAction($job, $recurrence);
        }
        parent::__construct($actions);
    }

}