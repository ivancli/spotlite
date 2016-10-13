<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/13/2016
 * Time: 2:21 PM
 */

namespace App\Validators\Dashboard\Dashboard;


use App\Exceptions\ValidationException;
use App\Validators\ValidatorAbstract;

class UpdateValidator extends ValidatorAbstract
{
    /**
     * Validate data with provided validation rules
     *
     * @param array $data
     * @param bool $throw
     * @return bool|\Illuminate\Support\MessageBag
     * @throws ValidationException
     */
    public function validate(array $data, $throw = true)
    {
        $rules = $this->getRules(isset($data['dashboard_id']) ? $data['dashboard_id'] : null, auth()->user()->getKey());
        $messages = $this->getMessages();
        $validation = $this->validator->make($data, $rules, $messages);
        if ($validation->fails()) {
            if ($throw) {
                throw new ValidationException($validation->messages());
            } else {
                return $validation->messages();
            }
        }
        return true;
    }

    /**
     * Get pre-set validation rules
     *
     * @param null $dashboardId
     * @param null $userId
     * @return array
     * @internal param null $id
     */
    protected function getRules($dashboardId = null, $userId = null)
    {
        return array(
            "dashboard_name" => "required|max:255|unique:dashboards,dashboard_name,$dashboardId,dashboard_id,user_id,$userId",
        );
    }

    protected function getMessages()
    {
        return array(
            "dashboard_name.required" => "Name of the new dashboard is required",
            "dashboard_name.max" => "Dashboard name accepts maximum 255 characters",
            "dashboard_name.unique" => "A dashboard with the same name already exists"
        );
    }
}