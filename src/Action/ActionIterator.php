<?php

namespace Scheduler\Action;

use Recurr\Recurrence;
use Recurr\Transformer\ArrayTransformer;
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
        $rRuleTransformer = new ArrayTransformer();
        $recurrenceCollection = $rRuleTransformer->transform($job->getRRule())->startsBetween($after, $before, $inc);
        $actions = [];
        /** @var Recurrence $recurrence */
        foreach ($recurrenceCollection as $recurrence) {
            $actions[] = new CallableAction($job, $recurrence->getStart());
        }
        parent::__construct($actions);
    }

}