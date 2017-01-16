<?php
//Login
Breadcrumbs::register('login', function ($breadcrumbs) {
    $breadcrumbs->push('Login', route('login.get'));
});

//Register
Breadcrumbs::register('register', function ($breadcrumbs) {
    $breadcrumbs->push('Register', route('register.get'));
});

//Register / Login
Breadcrumbs::register('register_login', function ($breadcrumbs) {
    $breadcrumbs->push('Register / Login', route('login.get'));
});

//Home
Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push('Home', url('/'));
});

/**
 * Dashboard
 */
Breadcrumbs::register('manage_dashboard', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Manage Dashboard', route('dashboard.manage'));
});


/**
 * User
 */

// Home > User
Breadcrumbs::register('user', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('User', route('um.user.index'));
});

// Home > User > Create user
Breadcrumbs::register('create_user', function ($breadcrumbs) {
    $breadcrumbs->parent('user');
    $breadcrumbs->push('Create user', route('um.user.create'));
});

// Home > User > Show user
Breadcrumbs::register('show_user', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('user');
    $breadcrumbs->push('User details', route('um.user.show', $user->getKey()));
});

// Home > User > Edit user
Breadcrumbs::register('edit_user', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('user');
    $breadcrumbs->push('Edit user', route('um.user.edit', $user->getKey()));
});

/**
 * Role
 */

// Home > Role
Breadcrumbs::register('role', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Role', route('um.role.index'));
});

// Home > Role > Create role
Breadcrumbs::register('create_role', function ($breadcrumbs) {
    $breadcrumbs->parent('role');
    $breadcrumbs->push('Create role', route('um.role.create'));
});

// Home > Role > Create role
Breadcrumbs::register('show_role', function ($breadcrumbs, $role) {
    $breadcrumbs->parent('role');
    $breadcrumbs->push('Role details', route('um.role.show', $role->getKey()));
});

// Home > Role > Create role
Breadcrumbs::register('edit_role', function ($breadcrumbs, $role) {
    $breadcrumbs->parent('role');
    $breadcrumbs->push('Edit role', route('um.role.edit', $role->getKey()));
});

/**
 * Permission
 */

// Home > Permission
Breadcrumbs::register('permission', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Permission', route('um.permission.index'));
});

// Home > Permission > Create permission
Breadcrumbs::register('create_permission', function ($breadcrumbs) {
    $breadcrumbs->parent('permission');
    $breadcrumbs->push('Create permission', route('um.permission.create'));
});

// Home > Permission > Show permission
Breadcrumbs::register('show_permission', function ($breadcrumbs, $permission) {
    $breadcrumbs->parent('permission');
    $breadcrumbs->push('Permission details', route('um.permission.show', $permission->getKey()));
});

// Home > Permission > Edit permission
Breadcrumbs::register('edit_permission', function ($breadcrumbs, $permission) {
    $breadcrumbs->parent('permission');
    $breadcrumbs->push('Edit permission', route('um.permission.edit', $permission->getKey()));
});

/**
 * Group
 */

// Home > Group
Breadcrumbs::register('group', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Group', route('um.group.index'));
});

// Home > Group > Create group
Breadcrumbs::register('create_group', function ($breadcrumbs) {
    $breadcrumbs->parent('group');
    $breadcrumbs->push('Create group', route('um.group.create'));
});

// Home > Group > Show group
Breadcrumbs::register('show_group', function ($breadcrumbs, $group) {
    $breadcrumbs->parent('group');
    $breadcrumbs->push('Group details', route('um.group.show', $group->getKey()));
});

// Home > Group > Edit group
Breadcrumbs::register('edit_group', function ($breadcrumbs, $group) {
    $breadcrumbs->parent('group');
    $breadcrumbs->push('Edit group', route('um.group.edit', $group->getKey()));
});

/**
 * Log
 */

Breadcrumbs::register('user_activity_log', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('User Activity Log', route('log.user_activity.index'));
});
Breadcrumbs::register('crawler_activity_log', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Crawler Activity Log', route('log.crawler_activity.index'));
});

/**
 * Profile
 */
Breadcrumbs::register('profile_index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('My Profile', route('profile.index'));
});
Breadcrumbs::register('profile_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('profile_index');
    $breadcrumbs->push('Edit Profile', route('profile.edit'));
});

Breadcrumbs::register('profile_show', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Profile', route('profile.show', $user->getKey()));
});


/**
 * Group
 */
Breadcrumbs::register('group_index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('My Groups', route('group.index'));
});
Breadcrumbs::register('group_create', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Create Group', route('group.create'));
});
Breadcrumbs::register('group_edit', function ($breadcrumbs, $group) {
    $breadcrumbs->parent('group_index');
    $breadcrumbs->push('Edit Group', route('group.edit', $group->getKey()));
});
Breadcrumbs::register('group_show', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Group', route('group.show', $user->getKey()));
});


/**
 * Settings
 */
Breadcrumbs::register('account_index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Settings', route('account.index'));
});


/**
 * Subscription settings
 */
Breadcrumbs::register('subscription_index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Subscription', route('subscription.index'));
});
Breadcrumbs::register('subscription_edit', function ($breadcrumbs, $subscription) {
    $breadcrumbs->parent('subscription_index');
    $breadcrumbs->push('Change My Plan', route('subscription.edit', $subscription->getKey()));
});

/**
 * Products
 */
Breadcrumbs::register('product_index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Products', route('product.index'));
});


/**
 * Admin Product Site
 */
Breadcrumbs::register('admin_site', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Site Management', route('admin.site.index'));
});
Breadcrumbs::register('admin_domain', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Domain Management', route('admin.domain.index'));
});
Breadcrumbs::register('admin_preference', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('App Preferences', route('admin.app_preference.index'));
});


/**
 * Report Page
 */
Breadcrumbs::register('report_index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Reports', route('report.index'));
});

/**
 * Alert Page
 */
Breadcrumbs::register('alert_index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Alerts', route('alert.index'));
});
