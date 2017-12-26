<?php

namespace Scheduler;

use Scheduler\Task\TaskInterface;

/**
 * Class Scheduler
 * @package Scheduler
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class Scheduler implements SchedulerInterface
{
    /**
     * @var TaskInterface[]
     */
    protected $tasks = [];

    /**
     * Scheduler constructor.
     * @param TaskInterface[] $tasks
     */
    public function __construct(array $tasks = [])
    {
        foreach ($tasks as $task) {
            $this->addTask($task);
        }
    }

    /**
     * @inheritdoc
     */
    public function getIterator(\DateTime $from, \DateTime $to = null, bool $inc = false): \Iterator
    {
        $tasks = [];
    }

    /**
     * @param TaskInterface $task
     * @return mixed|void
     */
    public function addTask(TaskInterface $task)
    {
        $this->tasks[$task];
    }
}