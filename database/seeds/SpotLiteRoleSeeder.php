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


        /**
         * client permission
         *
         * Manage Dashboard
         *  Create Dashboard, Read Dashboard, Update Dashboard, Delete Dashboard,
         *  Update Dashboard Preference, Delete Dashboard Preference
         *
         * Manage Dashboard Widget
         *  Create Dashboard Widget, Read Dashboard Widget, Update Dashboard Widget, Delete Dashboard Widget
         *
         * Manage Category
         *  Create Category, Read Category, Reorder Category, Update Category, Delete Category
         *
         * Manage Product
         *  Create Product, Read Product, Reorder Product, Update Product, Delete Product
         *
         * Manage Site
         *  Create Site, Read Site, Reorder Site, Get Site Price, Set My Price, Update Site, Delete Site
         *
         * Manage Chart
         *  Read Category Chart, Read Product Chart, Read Site Chart
         *
         * Manage Alert
         *  Read Product Alert, Update Product Alert, Delete Product Alert,
         *  Read Site Alert, Update Site Alert, Delete Site Alert
         *
         * Manage Report
         *  Read Report, Delete Report
         *
         * Manage Report Task
         *  Read Category Report Task, Update Category Report Task, Delete Category Report Task,
         *  Read Product Report Task, Update Product Report Task, Delete Product Report Task
         *
         */


        /* parent permissions */

        //MANAGE DASHBOARD
        $manageDashboard = new \Invigor\UM\UMPermission();
        $manageDashboard->name = "manage_dashboard";
        $manageDashboard->display_name = "Manage Dashboard";
        $manageDashboard->save();

        //MANAGE DASHBOARD WIDGET
        $manageDashboardWidget = new \Invigor\UM\UMPermission();
        $manageDashboardWidget->name = "manage_dashboard_widget";
        $manageDashboardWidget->display_name = "Manage Dashboard Widget";
        $manageDashboardWidget->save();

        //MANAGE CATEGORY
        $manageCategory = new \Invigor\UM\UMPermission();
        $manageCategory->name = "manage_category";
        $manageCategory->display_name = "Manage Category";
        $manageCategory->save();

        //MANAGE PRODUCT
        $manageProduct = new \Invigor\UM\UMPermission();
        $manageProduct->name = "manage_product";
        $manageProduct->display_name = "Manage Product";
        $manageProduct->save();

        //MANAGE SITE
        $manageSite = new \Invigor\UM\UMPermission();
        $manageSite->name = "manage_site";
        $manageSite->display_name = "Manage Site";
        $manageSite->save();

        //MANAGE CHART
        $manageChart = new \Invigor\UM\UMPermission();
        $manageChart->name = "manage_chart";
        $manageChart->display_name = "Manage Chart";
        $manageChart->save();

        //MANAGE ALERT
        $manageAlert = new \Invigor\UM\UMPermission();
        $manageAlert->name = "manage_alert";
        $manageAlert->display_name = "Manage Alert";
        $manageAlert->save();

        //MANAGE REPORT
        $manageReport = new \Invigor\UM\UMPermission();
        $manageReport->name = "manage_report";
        $manageReport->display_name = "Manage Report";
        $manageReport->save();

        //MANAGE REPORT TASK
        $manageReportTask = new \Invigor\UM\UMPermission();
        $manageReportTask->name = "manage_report_task";
        $manageReportTask->display_name = "Manage Report Task";
        $manageReportTask->save();


        /*child permissions*/

        //MANAGE DASHBOARD
        //*  Create Dashboard, Read Dashboard, Update Dashboard, Delete Dashboard,
        //*  Update Dashboard Preference, Delete Dashboard Preference
        $createDashboard = new \Invigor\UM\UMPermission();
        $createDashboard->name = "create_dashboard";
        $createDashboard->display_name = "Create Dashboard";
        $createDashboard->parent_id = $manageDashboard->getKey();
        $createDashboard->save();

        $readDashboard = new \Invigor\UM\UMPermission();
        $readDashboard->name = "read_dashboard";
        $readDashboard->display_name = "Read Dashboard";
        $readDashboard->parent_id = $manageDashboard->getKey();
        $readDashboard->save();

        $updateDashboard = new \Invigor\UM\UMPermission();
        $updateDashboard->name = "update_dashboard";
        $updateDashboard->display_name = "Update Dashboard";
        $updateDashboard->parent_id = $manageDashboard->getKey();
        $updateDashboard->save();

        $deleteDashboard = new \Invigor\UM\UMPermission();
        $deleteDashboard->name = "delete_dashboard";
        $deleteDashboard->display_name = "Delete Dashboard";
        $deleteDashboard->parent_id = $manageDashboard->getKey();
        $deleteDashboard->save();

        $updateDashboardPreference = new \Invigor\UM\UMPermission();
        $updateDashboardPreference->name = "update_dashboard_preference";
        $updateDashboardPreference->display_name = "Delete Dashboard";
        $updateDashboardPreference->parent_id = $manageDashboard->getKey();
        $updateDashboardPreference->save();

        $deleteDashboardPreference = new \Invigor\UM\UMPermission();
        $deleteDashboardPreference->name = "delete_dashboard_preference";
        $deleteDashboardPreference->display_name = "Delete Dashboard";
        $deleteDashboardPreference->parent_id = $manageDashboard->getKey();
        $deleteDashboardPreference->save();

        //MANAGE DASHBOARD WIDGET
        //*  Create Dashboard Widget, Read Dashboard Widget, Update Dashboard Widget, Delete Dashboard Widget
        $createDashboardWidget = new \Invigor\UM\UMPermission();
        $createDashboardWidget->name = "create_dashboard_widget";
        $createDashboardWidget->display_name = "Create Dashboard Widget";
        $createDashboardWidget->parent_id = $manageDashboardWidget->getKey();
        $createDashboardWidget->save();

        $readDashboardWidget = new \Invigor\UM\UMPermission();
        $readDashboardWidget->name = "read_dashboard_widget";
        $readDashboardWidget->display_name = "Read Dashboard Widget";
        $readDashboardWidget->parent_id = $manageDashboardWidget->getKey();
        $readDashboardWidget->save();

        $updateDashboardWidget = new \Invigor\UM\UMPermission();
        $updateDashboardWidget->name = "update_dashboard_widget";
        $updateDashboardWidget->display_name = "Update Dashboard Widget";
        $updateDashboardWidget->parent_id = $manageDashboardWidget->getKey();
        $updateDashboardWidget->save();

        $deleteDashboardWidget = new \Invigor\UM\UMPermission();
        $deleteDashboardWidget->name = "delete_dashboard_widget";
        $deleteDashboardWidget->display_name = "Delete Dashboard Widget";
        $deleteDashboardWidget->parent_id = $manageDashboardWidget->getKey();
        $deleteDashboardWidget->save();

        //MANAGE CATEGORY
        //*  Create Category, Read Category, Reorder Category, Update Category, Delete Category
        $createCategory = new \Invigor\UM\UMPermission();
        $createCategory->name = "create_category";
        $createCategory->display_name = "Create Category";
        $createCategory->parent_id = $manageCategory->getKey();
        $createCategory->save();

        $readCategory = new \Invigor\UM\UMPermission();
        $readCategory->name = "read_category";
        $readCategory->display_name = "Read Category";
        $readCategory->parent_id = $manageCategory->getKey();
        $readCategory->save();

        $reorderCategory = new \Invigor\UM\UMPermission();
        $reorderCategory->name = "reorder_category";
        $reorderCategory->display_name = "Reorder Category";
        $reorderCategory->parent_id = $manageCategory->getKey();
        $reorderCategory->save();

        $updateCategory = new \Invigor\UM\UMPermission();
        $updateCategory->name = "update_category";
        $updateCategory->display_name = "Update Category";
        $updateCategory->parent_id = $manageCategory->getKey();
        $updateCategory->save();

        $deleteCategory = new \Invigor\UM\UMPermission();
        $deleteCategory->name = "delete_category";
        $deleteCategory->display_name = "Delete Category";
        $deleteCategory->parent_id = $manageCategory->getKey();
        $deleteCategory->save();

        //MANAGE PRODUCT
        //*  Create Product, Read Product, Reorder Product, Update Product, Delete Product
        $createProduct = new \Invigor\UM\UMPermission();
        $createProduct->name = "create_product";
        $createProduct->display_name = "Create Product";
        $createProduct->parent_id = $manageProduct->getKey();
        $createProduct->save();

        $readProduct = new \Invigor\UM\UMPermission();
        $readProduct->name = "read_product";
        $readProduct->display_name = "Read Product";
        $readProduct->parent_id = $manageProduct->getKey();
        $readProduct->save();

        $reorderProduct = new \Invigor\UM\UMPermission();
        $reorderProduct->name = "reorder_product";
        $reorderProduct->display_name = "Reorder Product";
        $reorderProduct->parent_id = $manageProduct->getKey();
        $reorderProduct->save();

        $updateProduct = new \Invigor\UM\UMPermission();
        $updateProduct->name = "update_product";
        $updateProduct->display_name = "Update Product";
        $updateProduct->parent_id = $manageProduct->getKey();
        $updateProduct->save();

        $deleteProduct = new \Invigor\UM\UMPermission();
        $deleteProduct->name = "delete_product";
        $deleteProduct->display_name = "Delete Product";
        $deleteProduct->parent_id = $manageProduct->getKey();
        $deleteProduct->save();

        //MANAGE SITE
        //*  Create Site, Read Site, Reorder Site, Get Site Price, Set My Price, Update Site, Delete Site
        $createSite = new \Invigor\UM\UMPermission();
        $createSite->name = "create_site";
        $createSite->display_name = "Create Site";
        $createSite->parent_id = $manageSite->getKey();
        $createSite->save();

        $readSite = new \Invigor\UM\UMPermission();
        $readSite->name = "read_site";
        $readSite->display_name = "Read Site";
        $readSite->parent_id = $manageSite->getKey();
        $readSite->save();

        $reorderSite = new \Invigor\UM\UMPermission();
        $reorderSite->name = "reorder_site";
        $reorderSite->display_name = "Reorder Site";
        $reorderSite->parent_id = $manageSite->getKey();
        $reorderSite->save();

        $updateSite = new \Invigor\UM\UMPermission();
        $updateSite->name = "update_site";
        $updateSite->display_name = "Update Site";
        $updateSite->parent_id = $manageSite->getKey();
        $updateSite->save();

        $deleteSite = new \Invigor\UM\UMPermission();
        $deleteSite->name = "delete_site";
        $deleteSite->display_name = "Delete Site";
        $deleteSite->parent_id = $manageSite->getKey();
        $deleteSite->save();

        $getSitePrice = new \Invigor\UM\UMPermission();
        $getSitePrice->name = "get_site_price";
        $getSitePrice->display_name = "Get Site Price";
        $getSitePrice->parent_id = $manageSite->getKey();
        $getSitePrice->save();

        $setMyPrice = new \Invigor\UM\UMPermission();
        $setMyPrice->name = "set_my_price";
        $setMyPrice->display_name = "Set My Price";
        $setMyPrice->parent_id = $manageSite->getKey();
        $setMyPrice->save();

        //MANAGE CHART
        //*  Read Category Chart, Read Product Chart, Read Site Chart
        $readCategoryChart = new \Invigor\UM\UMPermission();
        $readCategoryChart->name = "read_category_chart";
        $readCategoryChart->display_name = "Read Category Chart";
        $readCategoryChart->parent_id = $manageChart->getKey();
        $readCategoryChart->save();

        $readProductChart = new \Invigor\UM\UMPermission();
        $readProductChart->name = "read_product_chart";
        $readProductChart->display_name = "Read Product Chart";
        $readProductChart->parent_id = $manageChart->getKey();
        $readProductChart->save();

        $readSiteChart = new \Invigor\UM\UMPermission();
        $readSiteChart->name = "read_site_chart";
        $readSiteChart->display_name = "Read Site Chart";
        $readSiteChart->parent_id = $manageChart->getKey();
        $readSiteChart->save();

        //MANAGE ALERT
        //*  Read Product Alert, Update Product Alert, Delete Product Alert,
        //*  Read Site Alert, Update Site Alert, Delete Site Alert
        $readProductAlert = new \Invigor\UM\UMPermission();
        $readProductAlert->name = "read_product_alert";
        $readProductAlert->display_name = "Read Product Alert";
        $readProductAlert->parent_id = $manageAlert->getKey();
        $readProductAlert->save();

        $updateProductAlert = new \Invigor\UM\UMPermission();
        $updateProductAlert->name = "update_product_alert";
        $updateProductAlert->display_name = "Update Product Alert";
        $updateProductAlert->parent_id = $manageAlert->getKey();
        $updateProductAlert->save();

        $deleteProductAlert = new \Invigor\UM\UMPermission();
        $deleteProductAlert->name = "delete_product_alert";
        $deleteProductAlert->display_name = "Delete Product Alert";
        $deleteProductAlert->parent_id = $manageAlert->getKey();
        $deleteProductAlert->save();

        $readSiteAlert = new \Invigor\UM\UMPermission();
        $readSiteAlert->name = "read_site_alert";
        $readSiteAlert->display_name = "Read Site Alert";
        $readSiteAlert->parent_id = $manageAlert->getKey();
        $readSiteAlert->save();

        $updateSiteAlert = new \Invigor\UM\UMPermission();
        $updateSiteAlert->name = "update_site_alert";
        $updateSiteAlert->display_name = "Update Site Alert";
        $updateSiteAlert->parent_id = $manageAlert->getKey();
        $updateSiteAlert->save();

        $deleteSiteAlert = new \Invigor\UM\UMPermission();
        $deleteSiteAlert->name = "delete_site_alert";
        $deleteSiteAlert->display_name = "Delete Site Alert";
        $deleteSiteAlert->parent_id = $manageAlert->getKey();
        $deleteSiteAlert->save();

        //MANAGE REPORT
        //*  Read Report, Delete Report
        $readReport = new \Invigor\UM\UMPermission();
        $readReport->name = "read_report";
        $readReport->display_name = "Read Report";
        $readReport->parent_id = $manageReport->getKey();
        $readReport->save();

        $deleteReport = new \Invigor\UM\UMPermission();
        $deleteReport->name = "delete_report";
        $deleteReport->display_name = "Delete Report";
        $deleteReport->parent_id = $manageReport->getKey();
        $deleteReport->save();

        //MANAGE REPORT TASK
        //*  Read Category Report Task, Update Category Report Task, Delete Category Report Task,
        //*  Read Product Report Task, Update Product Report Task, Delete Product Report Task
        $readCategoryReportTask = new \Invigor\UM\UMPermission();
        $readCategoryReportTask->name = "read_category_report_task";
        $readCategoryReportTask->display_name = "Read Category Report Task";
        $readCategoryReportTask->parent_id = $manageReportTask->getKey();
        $readCategoryReportTask->save();

        $updateCategoryReportTask = new \Invigor\UM\UMPermission();
        $updateCategoryReportTask->name = "update_category_report_task";
        $updateCategoryReportTask->display_name = "Update Category Report Task";
        $updateCategoryReportTask->parent_id = $manageReportTask->getKey();
        $updateCategoryReportTask->save();

        $deleteCategoryReportTask = new \Invigor\UM\UMPermission();
        $deleteCategoryReportTask->name = "delete_category_report_task";
        $deleteCategoryReportTask->display_name = "Delete Category Report Task";
        $deleteCategoryReportTask->parent_id = $manageReportTask->getKey();
        $deleteCategoryReportTask->save();

        $readProductReportTask = new \Invigor\UM\UMPermission();
        $readProductReportTask->name = "read_product_report_task";
        $readProductReportTask->display_name = "Read Product Report Task";
        $readProductReportTask->parent_id = $manageReportTask->getKey();
        $readProductReportTask->save();

        $updateProductReportTask = new \Invigor\UM\UMPermission();
        $updateProductReportTask->name = "update_product_report_task";
        $updateProductReportTask->display_name = "Update Product Report Task";
        $updateProductReportTask->parent_id = $manageReportTask->getKey();
        $updateProductReportTask->save();

        $deleteProductReportTask = new \Invigor\UM\UMPermission();
        $deleteProductReportTask->name = "delete_product_report_task";
        $deleteProductReportTask->display_name = "Delete Product Report Task";
        $deleteProductReportTask->parent_id = $manageReportTask->getKey();
        $deleteProductReportTask->save();

        $client->attachPermissions(array($manageDashboard, $manageDashboardWidget, $manageCategory, $manageProduct, $manageSite, $manageChart, $manageAlert, $manageReport, $manageReportTask));
        $superAdmin->attachPermissions(array($manageDashboard, $manageDashboardWidget, $manageCategory, $manageProduct, $manageSite, $manageChart, $manageAlert, $manageReport, $manageReportTask));
        $tier_1->attachPermissions(array($manageDashboard, $manageDashboardWidget, $manageCategory, $manageProduct, $manageSite, $manageChart, $manageAlert, $manageReport, $manageReportTask));
        $tier_2->attachPermissions(array($manageDashboard, $manageDashboardWidget, $manageCategory, $manageProduct, $manageSite, $manageChart, $manageAlert, $manageReport, $manageReportTask));



        /*subscription permissions*/

    }
}