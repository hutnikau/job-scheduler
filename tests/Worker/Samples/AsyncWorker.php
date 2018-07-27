<?php

namespace SchedulerTests\Worker\Samples;

use Scheduler\ActionInspector\FileActionInspector;
use Scheduler\Scheduler;
use Scheduler\JobRunner\JobRunner;
use Scheduler\Worker\Worker;

require_once __DIR__.'/../../../vendor/autoload.php';

array_shift($argv);
$start = $argv[0];
$maxIterations = intval($argv[1]);
$interval = $argv[2];

/** @var array $jobs */
require_once __DIR__.'/jobs.php';

$scheduler = new Scheduler($jobs);
$actionsLog = new FileActionInspector(__DIR__ . DIRECTORY_SEPARATOR . 'actions.log');
$jobRunner = new JobRunner($actionsLog);
$worker = new Worker($jobRunner, $scheduler);
$worker->setMaxIterations($maxIterations);
$worker->run($start, $interval);