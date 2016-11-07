<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/5/2016
 * Time: 1:05 PM
 */

namespace App\Listeners\Products;


use App\Contracts\Repository\Mailer\MailingAgentContract;
use App\Jobs\LogReportActivity;
use App\Jobs\LogUserActivity;

class ReportEventSubscriber
{
    protected $mailingAgentRepo;

    public function __construct(MailingAgentContract $mailingAgentContract)
    {
        $this->mailingAgentRepo = $mailingAgentContract;
    }

    public function onReportCreated($event)
    {
    }

    public function onReportListViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed report list page"))->onQueue("logging"));
    }

    public function onReportTaskCreated($event)
    {
        $reportTask = $event->reportTask;
        $this->mailingAgentRepo->updateLastSetupReportDate();
        dispatch((new LogUserActivity(auth()->user(), "created report task - {$reportTask->getKey()}"))->onQueue("logging"));
    }

    public function onReportTaskCreating($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "creating report task"))->onQueue("logging"));
    }

    public function onReportTaskDeleting($event)
    {
        $reportTask = $event->reportTask;
        dispatch((new LogUserActivity(auth()->user(), "deleting report task - {$reportTask->getKey()}"))->onQueue("logging"));
    }

    public function onReportTaskEditing($event)
    {
        $reportTask = $event->reportTask;
        dispatch((new LogUserActivity(auth()->user(), "editing report task - {$reportTask->getKey()}"))->onQueue("logging"));
    }

    public function onReportTriggered($event)
    {
        $reportTask = $event->reportTask;
        dispatch((new LogReportActivity($reportTask, array(
            "type" => "trigger"
        )))->onQueue("logging"));
    }

    public function onReportCreating($event)
    {
        $reportTask = $event->reportTask;
        dispatch((new LogReportActivity($reportTask, array(
            "type" => "create",
        )))->onQueue("logging"));
    }

    public function onReportSent($event)
    {
        $reportTask = $event->reportTask;
        $reportEmail = $event->reportEmail;
        dispatch((new LogReportActivity($reportTask, array(
            "type" => "sent",
            "content" => $reportEmail
        )))->onQueue("logging"));
    }

    public function onReportTaskCreateViewed($event)
    {
        dispatch((new LogUserActivity(auth()->user(), "viewed report task create page"))->onQueue("logging"));
    }

    public function onReportTaskDeleted($event)
    {
        $reportTask = $event->reportTask;
        dispatch((new LogUserActivity(auth()->user(), "deleted report task {$reportTask->getKey()}"))->onQueue("logging"));
    }

    public function onReportTaskEdited($event)
    {
        $reportTask = $event->reportTask;
        $this->mailingAgentRepo->updateLastSetupReportDate();
        dispatch((new LogUserActivity(auth()->user(), "edited report task - {$reportTask->getKey()}"))->onQueue("logging"));
    }

    public function onReportTaskEditViewed($event)
    {
        $reportTask = $event->reportTask;
        dispatch((new LogUserActivity(auth()->user(), "viewed report task edit page - {$reportTask->getKey()}"))->onQueue("logging"));
    }

    public function subscribe($events)
    {
        $events->listen('App\Events\Products\Report\ReportCreated', 'App\Listeners\Products\ReportEventSubscriber@onReportCreated');
        $events->listen('App\Events\Products\Report\ReportListViewed', 'App\Listeners\Products\ReportEventSubscriber@onReportListViewed');
        $events->listen('App\Events\Products\Report\ReportTaskCreated', 'App\Listeners\Products\ReportEventSubscriber@onReportTaskCreated');
        $events->listen('App\Events\Products\Report\ReportTaskCreating', 'App\Listeners\Products\ReportEventSubscriber@onReportTaskCreating');
        $events->listen('App\Events\Products\Report\ReportTaskDeleting', 'App\Listeners\Products\ReportEventSubscriber@onReportTaskDeleting');
        $events->listen('App\Events\Products\Report\ReportTaskEditing', 'App\Listeners\Products\ReportEventSubscriber@onReportTaskEditing');
        $events->listen('App\Events\Products\Report\ReportTriggered', 'App\Listeners\Products\ReportEventSubscriber@onReportTriggered');
        $events->listen('App\Events\Products\Report\ReportCreating', 'App\Listeners\Products\ReportEventSubscriber@onReportCreating');
        $events->listen('App\Events\Products\Report\ReportSent', 'App\Listeners\Products\ReportEventSubscriber@onReportSent');
        $events->listen('App\Events\Products\Report\ReportTaskCreateViewed', 'App\Listeners\Products\ReportEventSubscriber@onReportTaskCreateViewed');
        $events->listen('App\Events\Products\Report\ReportTaskDeleted', 'App\Listeners\Products\ReportEventSubscriber@onReportTaskDeleted');
        $events->listen('App\Events\Products\Report\ReportTaskEdited', 'App\Listeners\Products\ReportEventSubscriber@onReportTaskEdited');
        $events->listen('App\Events\Products\Report\ReportTaskEditViewed', 'App\Listeners\Products\ReportEventSubscriber@onReportTaskEditViewed');
    }
}














