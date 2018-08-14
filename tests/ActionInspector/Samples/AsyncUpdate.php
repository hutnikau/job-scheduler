<?php

namespace SchedulerTests\Worker\Samples;

use Scheduler\ActionInspector\RdsActionInspector;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;

require_once __DIR__.'/../../../vendor/autoload.php';

array_shift($argv);
$processId = $argv[0];
$start = $argv[1];

/** @var array $actions */
require_once __DIR__.'/actions.php';

$connectionParams = array(
    'url' => 'sqlite:///' .__DIR__ . DIRECTORY_SEPARATOR . 'db.sqlite',
    'driver' => 'pdo_sqlite',
);
$connection = DriverManager::getConnection($connectionParams, new Configuration());
$actionsInspector = new RdsActionInspector($connection);
foreach ($actions as $action) {
    if ($actionsInspector->update($action)) {
        $action();
        $actionsInspector->update($action);
    };
}
