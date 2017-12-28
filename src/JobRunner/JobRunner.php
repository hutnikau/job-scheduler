<?php

namespace Scheduler\JobRunner;

use Scheduler\SchedulerInterface;
use DateTimeInterface;
use Scheduler\Action\Report;

/**
 * Class TaskRunner
 * @package Scheduler\TaskRunner
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class JobRunner implements JobRunnerInterface
{
    /**
     * @inheritdoc
     */
    public function run(SchedulerInterface $scheduler, DateTimeInterface $from, DateTimeInterface $to = null, $inc = true)
    {
        $actionsIterator = $scheduler->getIterator($from, $to, $inc);
        $reports = [];
        foreach ($actionsIterator as $action) {
            try {
                $reports[] = new Report($action, $action());
            } catch (\Exception $e) {
                $reports[] = new Report($action, $e, Report::TYPE_ERROR);
            }
        }
        return $reports;
    }
}