<?php
namespace App\Contracts\GroupManagement;
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 4/09/2016
 * Time: 4:17 PM
 */
interface GroupManager
{
    /**
     * Load list of groups
     * @return mixed
     */
    public function getGroups();

    /**
     * Load group by group id
     * @param $id
     * @return mixed
     */
    public function getGroup($id);

    /**
     * Create new group
     * @param $options
     * @return mixed
     */
    public function createGroup($options);

    /**
     * Update group by group id
     * @param $id
     * @param $options
     * @return mixed
     */
    public function updateGroup($id, $options);

    /**
     * Delete group by group id
     * @param $id
     * @return mixed
     */
    public function destroyGroup($id);
}