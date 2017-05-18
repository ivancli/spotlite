<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 18/05/2017
 * Time: 12:48 PM
 */

namespace App\Console\Commands;


use App\Models\Category;
use App\Models\User;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class FullReport extends Command
{
    protected $signature = "full-report {user_id}";
    protected $description = 'Pushing available crawlers to queue';

    protected $crawler = null;

    public function handle()
    {
        $user_id = $this->argument('user_id');
        if(is_null($user_id)){
            $this->output->error('user_id not found');
            return false;
        }
        $categories = Category::where('user_id', '=', $user_id);
        $data = $categories;
        $fileName = "full_report";
        $excel = Excel::create("{$user_id}_{$fileName}", function ($excel) use ($categories, $data, $fileName) {
            $excel->sheet("full_report", function ($sheet) use ($data) {
                $sheet->loadview('products.report.excel.user', compact(['data']));
                $sheet->setColumnFormat(array(
                    'D' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                    'E' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2,
                    'F' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                    'G' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2,
                ));
                $sheet->setWidth('A', 30);
                $sheet->setWidth('B', 30);
                $sheet->setWidth('C', 30);
                $sheet->setWidth('D', 20);
                $sheet->setWidth('E', 20);
                $sheet->setWidth('F', 20);
                $sheet->setWidth('G', 20);
            });
        })->store('csv');

    }
}