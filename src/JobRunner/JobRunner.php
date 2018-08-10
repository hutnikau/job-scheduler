<?php

namespace Scheduler\JobRunner;

use Scheduler\ActionInspector\ActionInspectorInterface;
use Scheduler\SchedulerInterface;
use DateTimeInterface;
use Scheduler\Action\Report;

/**
 * Class JobRunner
 * @package Scheduler\JobRunner
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class JobRunner implements JobRunnerInterface
{

    /** @var ActionInspectorInterface */
    protected $actionLog;

    /**
     * JobRunner constructor.
     * @param ActionInspectorInterface|null $actionLog
     */
    public function __construct(ActionInspectorInterface $actionLog = null)
    {
        $this->actionLog = $actionLog;
    }

    /**
     * @inheritdoc
     */
    public function run(SchedulerInterface $scheduler, DateTimeInterface $from, DateTimeInterface $to = null, $inc = true)
    {
        $actionsIterator = $scheduler->getIterator($from, $to, $inc);
        $reports = [];
        foreach ($actionsIterator as $action) {
            try {
                if ($this->actionLog === null) {
                    $reports[] = new Report($action, $action());
                } else if ($this->actionLog->update($action)) {
                    $reports[] = new Report($action, $action());
                    $this->actionLog->update($action);
                } else {
                    //action already executed or taken by another worker
                }
            } catch (\Exception $e) {
                $reports[] = new Report($action, $e, Report::TYPE_ERROR);
            }
        }
        return $reports;
    }
}