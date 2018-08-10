<?php

namespace SchedulerTests\Worker\Samples;

use Scheduler\ActionInspector\FileActionInspector;
use Scheduler\Scheduler;
use Scheduler\JobRunner\JobRunner;
use Scheduler\Worker\Worker;

require_once __DIR__.'/../../../vendor/autoload.php';

array_shift($argv);
$start = $argv[0];
$interval = $argv[2];

/** @var array $jobs */
require_once __DIR__.'/jobs.php';

$scheduler = new Scheduler($jobs);
$actionsInspector = new FileActionInspector(__DIR__ . DIRECTORY_SEPARATOR . 'actions.log');
$jobRunner = new JobRunner($actionsInspector);
$worker = new Worker($jobRunner, $scheduler);
$worker->setMaxIterations(1);
$worker->run($start, $interval);
