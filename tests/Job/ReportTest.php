<?php

namespace SchedulerTests\Job;

use PHPUnit\Framework\TestCase;
use Scheduler\Action\Report;
use Scheduler\Action\ActionInterface;

/**
 * Class ReportTest
 * @package SchedulerTests\Job
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class ReportTest extends TestCase
{
    public function testGetResult()
    {
        $actionProphesy = $this->prophesize(ActionInterface::class);
        $report = new Report($actionProphesy->reveal(), 'foo');
        $this->assertEquals('foo', $report->getResult());
    }

    public function testGetAction()
    {
        $actionProphesy = $this->prophesize(ActionInterface::class);
        $action = $actionProphesy->reveal();
        $report = new Report($action, 'foo');
        $this->assertEquals($action, $report->getAction());
    }

    public function testGetType()
    {
        $actionProphesy = $this->prophesize(ActionInterface::class);
        $report = new Report($actionProphesy->reveal(), 'foo');
        $this->assertEquals(Report::TYPE_SUCCESS, $report->getType());
    }

}
