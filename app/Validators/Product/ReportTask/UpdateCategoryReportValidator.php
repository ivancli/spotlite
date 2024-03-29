<?php
namespace App\Validators\Product\ReportTask;

use App\Validators\ValidatorAbstract;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/23/2016
 * Time: 3:01 PM
 */
class UpdateCategoryReportValidator extends ValidatorAbstract
{

    /**
     * Get pre-set validation rules
     *
     * @param null $id
     * @return array
     */
    protected function getRules($id = null)
    {
        return array(
            "frequency" => "required",
            "time" => "required_if:frequency,daily",
            "day" => "required_if:frequency,weekly",
            "date" => "required_if:frequency,monthly",
            "email" => "required|array",
            "email.*" => "email",
        );
    }

}