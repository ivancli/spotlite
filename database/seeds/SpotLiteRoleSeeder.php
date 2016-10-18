<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SpotLiteRoleSeeder extends Seeder
{

    public function run()
    {
        $superAdmin = new \Invigor\UM\UMRole();
        $superAdmin->name = "super_admin";
        $superAdmin->display_name = "Super Admin";
        $superAdmin->save();

        $tier_1 = new \Invigor\UM\UMRole();
        $tier_1->name = "super_admin";
        $tier_1->display_name = "Super Admin";
        $tier_1->save();

        $tier_1 = new \Invigor\UM\UMRole();
        $tier_1->name = "tier_1";
        $tier_1->display_name = "Tier 1 Admin";
        $tier_1->save();

        $tier_2 = new \Invigor\UM\UMRole();
        $tier_2->name = "tier_2";
        $tier_2->display_name = "Tier 2 Staff";
        $tier_2->save();

        $client = new \Invigor\UM\UMRole();
        $client->name = "client";
        $client->display_name = "Client";
        $client->save();




        /* parent permissions */
        $manageUser = new \Invigor\UM\UMPermission();
        $manageUser->name = "manage_user";
        $manageUser->display_name = "Manage User";
        $manageUser->save();

        $manageGroup = new \Invigor\UM\UMPermission();
        $manageGroup->name = "manage_group";
        $manageGroup->display_name = "Manage Group";
        $manageGroup->save();

        $manageRole = new \Invigor\UM\UMPermission();
        $manageRole->name = "manage_role";
        $manageRole->display_name = "Manage Role";
        $manageRole->save();

        $managePermission = new \Invigor\UM\UMPermission();
        $managePermission->name = "manage_permission";
        $managePermission->display_name = "Manage Permission";
        $managePermission->save();

        /*child permissions*/
        //user
        $createUser = new \Invigor\UM\UMPermission();
        $createUser->name = "create_user";
        $createUser->display_name = "Create User";
        $createUser->parent_id = $manageUser->permission_id;
        $createUser->save();
        $readUser = new \Invigor\UM\UMPermission();
        $readUser->name = "read_user";
        $readUser->display_name = "Read User";
        $readUser->parent_id = $manageUser->permission_id;
        $readUser->save();
        $updateUser = new \Invigor\UM\UMPermission();
        $updateUser->name = "update_user";
        $updateUser->display_name = "Update User";
        $updateUser->parent_id = $manageUser->permission_id;
        $updateUser->save();
        $deleteUser = new \Invigor\UM\UMPermission();
        $deleteUser->name = "delete_user";
        $deleteUser->display_name = "Delete User";
        $deleteUser->parent_id = $manageUser->permission_id;
        $deleteUser->save();
        //group
        $createGroup = new \Invigor\UM\UMPermission();
        $createGroup->name = "create_group";
        $createGroup->display_name = "Create Group";
        $createGroup->parent_id = $manageGroup->permission_id;
        $createGroup->save();
        $readGroup = new \Invigor\UM\UMPermission();
        $readGroup->name = "read_group";
        $readGroup->display_name = "Read Group";
        $readGroup->parent_id = $manageGroup->permission_id;
        $readGroup->save();
        $updateGroup = new \Invigor\UM\UMPermission();
        $updateGroup->name = "update_group";
        $updateGroup->display_name = "Update Group";
        $updateGroup->parent_id = $manageGroup->permission_id;
        $updateGroup->save();
        $deleteGroup = new \Invigor\UM\UMPermission();
        $deleteGroup->name = "delete_group";
        $deleteGroup->display_name = "Delete Group";
        $deleteGroup->parent_id = $manageGroup->permission_id;
        $deleteGroup->save();
        //role
        $createRole = new \Invigor\UM\UMPermission();
        $createRole->name = "create_role";
        $createRole->display_name = "Create Role";
        $createRole->parent_id = $manageRole->permission_id;
        $createRole->save();
        $readRole = new \Invigor\UM\UMPermission();
        $readRole->name = "read_role";
        $readRole->display_name = "Read Role";
        $readRole->parent_id = $manageRole->permission_id;
        $readRole->save();
        $updateRole = new \Invigor\UM\UMPermission();
        $updateRole->name = "update_role";
        $updateRole->display_name = "Update Role";
        $updateRole->parent_id = $manageRole->permission_id;
        $updateRole->save();
        $deleteRole = new \Invigor\UM\UMPermission();
        $deleteRole->name = "delete_role";
        $deleteRole->display_name = "Delete Role";
        $deleteRole->parent_id = $manageRole->permission_id;
        $deleteRole->save();
        //permission
        $createPermission = new \Invigor\UM\UMPermission();
        $createPermission->name = "create_permission";
        $createPermission->display_name = "Create Permission";
        $createPermission->parent_id = $managePermission->permission_id;
        $createPermission->save();
        $readPermission = new \Invigor\UM\UMPermission();
        $readPermission->name = "read_permission";
        $readPermission->display_name = "Read Permission";
        $readPermission->parent_id = $managePermission->permission_id;
        $readPermission->save();
        $updatePermission = new \Invigor\UM\UMPermission();
        $updatePermission->name = "update_permission";
        $updatePermission->display_name = "Update Permission";
        $updatePermission->parent_id = $managePermission->permission_id;
        $updatePermission->save();
        $deletePermission = new \Invigor\UM\UMPermission();
        $deletePermission->name = "delete_permission";
        $deletePermission->display_name = "Delete Permission";
        $deletePermission->parent_id = $managePermission->permission_id;
        $deletePermission->save();

        //attach permissions
        $superAdmin->attachPermissions(array($manageUser, $manageGroup, $manageRole, $managePermission));
    }
}