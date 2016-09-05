<?php
namespace App\Repositories\GroupManagement;

use App\Contracts\GroupManagement\GroupManager;
use App\Models\Group;

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
        return Group::all();
    }

    /**
     * Load group by group id
     * @param $id
     * @return mixed
     */
    public function getGroup($id)
    {
        return Group::findOrFail($id);
    }

    /**
     * Create new group
     * @param $options
     * @return mixed
     */
    public function createGroup($options)
    {
        /*TODO implement validation here*/
        $group = Group::where('name', $options['name'])->first();
        if (is_null($group)) {
            $group = Group::create($options);
        }
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
        $group = Group::findOrFail($id);
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
        $group = Group::findOrFail($id);
        $group->delete();
        return true;
    }
}