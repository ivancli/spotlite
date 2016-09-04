<?php
namespace App\Repositories\GroupManagement;

use App\Contracts\GroupManagement\GroupManager;
use Invigor\UM\UMGroup;

/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 4/09/2016
 * Time: 4:20 PM
 */
class UMGroupManager implements GroupManager
{

    /**
     * Load list of groups
     * @return mixed
     */
    public function getGroups()
    {
        return UMGroup::all();
    }

    /**
     * Load group by group id
     * @param $id
     * @return mixed
     */
    public function getGroup($id)
    {
        return UMGroup::findOrFail($id);
    }

    /**
     * Create new group
     * @param $options
     * @return mixed
     */
    public function createGroup($options)
    {
        /*TODO implement validation here*/
        $group = UMGroup::create($options);
        return $group;
    }

    /**
     * Update group by group id
     * @param $id
     * @param $options
     * @return mixed
     */
    public function updateGroup($id, $options)
    {
        $group = UMGroup::findOrFail($id);
        /*TODO implement validation here*/
        $group->update($options);
        return $group;
    }

    /**
     * Delete group by group id
     * @param $id
     * @return mixed
     */
    public function destroyGroup($id)
    {
        // TODO: Implement destroyGroup() method.
        $group = UMGroup::findOrFail($id);
        $group->delete();
        return true;
    }
}