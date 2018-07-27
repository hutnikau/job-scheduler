<?php

namespace Scheduler\Worker;

/**
 * Interface WorkerInterface
 * @package Scheduler\Worker
 */
interface WorkerInterface
{
    /**
     * Start processing jobs
     *
     * @param $startTime
     * @param $interval
     * @return mixed
     */
    public function run($startTime, $interval);

    /**
     * @return boolean
     */
    public function isRunning();

    /**
     * @return mixed
     */
    public function shutdown();

    /**
     * @param $iterations
     * @return mixed
     */
    public function setMaxIterations($iterations);
}