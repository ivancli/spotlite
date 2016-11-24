<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/10/2016
 * Time: 3:44 PM
 */

namespace App\Console\Commands;


use App\Jobs\SendReport;
use App\Models\AppPreference;
use App\Models\ReportTask;
use Illuminate\Console\Command;

class Report extends Command
{
    protected $signature = "report:run";

    protected $crawler = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        /* check in every hour */
        $lastReservedAt = AppPreference::getReportLastReservedAt();
        $lastReservedRoundedHours = date("Y-m-d H:00:00", strtotime($lastReservedAt));
        $currentRoundedHours = date("Y-m-d H:00:00");
        if (AppPreference::getReportReserved() == 'n' && (is_null($lastReservedAt) || intval((strtotime($currentRoundedHours) - strtotime($lastReservedRoundedHours)) / 3600) > 0)) {
            /*reserve the task*/
            AppPreference::setReportReserved();
            AppPreference::setReportLastReservedAt();

            /*LOOP THROUGH ALL REPORT TASKS AND TRIGGER THE DUE REPORT TASKS*/
            $reportTasks = ReportTask::all();

            foreach ($reportTasks as $reportTask) {
                switch ($reportTask->frequency) {
                    case "daily":
                        //check report not yet sent today
                        $lastSentAt = date("Y-m-d 00:00:00", strtotime($reportTask->last_sent_at));
                        $currentRoundedDate = date("Y-m-d 00:00:00");

                        /*if last sent date is at least 1 day ahead current date*/
                        if (is_null($reportTask->last_sent_at) || (intval((strtotime($currentRoundedDate) - strtotime($lastSentAt)) / 3600) > 0)) {
                            $lastSentRoundedDay = date("N", strtotime($reportTask->last_sent_at));
                            $currentRoundedDay = date("N");
                            if (($currentRoundedDay == 6 || $currentRoundedDay == 7) && $reportTask->weekday_only == 'y') {
                                continue;
                            }
                            /*
                             * precision set to be HOUR
                             * replace 00 with i to increase precision to be MINUTE
                             */
                            // check report time = current time
                            $currentRoundedMinute = date("H:00:00");
                            if ($reportTask->time == $currentRoundedMinute) {
                                $reportTask->setLastSentStamp();
                                dispatch((new SendReport($reportTask))->onQueue("reporting"));
                            }
                        }
                        break;
                    case "weekly":
                        // check report not yet sent this week
                        $lastSentAt = date('Y-\WW', strtotime($reportTask->last_sent_at));
                        $currentRoundedWeek = date('Y-\WW');
                        if (is_null($reportTask->last_sent_at) || (intval((strtotime($currentRoundedWeek) - strtotime($lastSentAt)) / 3600) > 0)) {
                            $currentRoundedDay = date("N");

                            // check report day = current day
                            if ($reportTask->day == $currentRoundedDay) {
                                $reportTask->setLastSentStamp();
                                dispatch((new SendReport($reportTask))->onQueue("reporting"));
                            }
                        }
                        break;
                    case "monthly":
                        // check report not yet sent this month
                        $lastSentAt = date('Y-m', strtotime($reportTask->last_sent_at));
                        $currentRoundedMonth = date('Y-m');
                        if (is_null($reportTask->last_sent_at) || (intval((strtotime($currentRoundedMonth) - strtotime($lastSentAt)) / 3600) > 0)) {

                            // check report date = current date
                            $currentRoundedDate = date("d");
                            $numberOfDaysInThisMonth = date("t");

                            // today is the scheduled report date OR (scheduled report date is greater than number of days of this month AND today is the last day of this month)
                            // e.g. scheduled 31, runs on 30 of Sep, runs on 28/29 of Feb
                            if ($reportTask->date == $currentRoundedDate || ($numberOfDaysInThisMonth < $reportTask->date && $numberOfDaysInThisMonth == $currentRoundedDate)) {
                                $reportTask->setLastSentStamp();
                                dispatch((new SendReport($reportTask))->onQueue("reporting"));
                            }
                        }
                        break;
                    default:
                        return false;
                }
            }
            AppPreference::setReportReserved('n');
        }
    }
}