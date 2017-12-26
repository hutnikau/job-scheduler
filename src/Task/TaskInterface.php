<?php

namespace Scheduler\Task;

/**
 * Interface TaskInterface
 * @package Task
 */
interface TaskInterface
{
    /**
     * @return mixed
     */
    public function __invoke();

}