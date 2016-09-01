<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            @if(auth()->check() && (auth()->user()->hasValidSubscription() || auth()->user()->isStaff()))
                <li class="{{Style::set_active('/')}}">
                    <a href="{{url('/')}}">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{Style::set_active_starts_with('product')}}"><a href="{{url('product')}}"><i
                                class="fa fa-square-o"></i> <span>Products</span></a></li>
                <li class="treeview {{Style::set_active_or(array('report', 'alert'))}}">
                    <a href="#">
                        <i class="fa fa-envelope"></i>
                        <span>Reports and Alerts</span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{Style::set_active('report')}}"><a href="{{url('report')}}"><i
                                        class="fa fa-line-chart"></i> Reports</a></li>
                        <li class="{{Style::set_active('alert')}}"><a href="{{url('alert')}}"><i
                                        class="fa fa-bell-o"></i> Alerts</a></li>
                    </ul>
                </li>
            @endif
            @if(auth()->check() && auth()->user()->isStaff())
                <li class="treeview {{Style::set_active_starts_with(array('domain', 'site'))}}">
                    <a href="#">
                        <i class="fa fa-files-o"></i>
                        <span>Crawler Management</span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{Style::set_active_starts_with('domain')}}"><a href="{{url('domain')}}"><i
                                        class="fa fa-circle-o"></i> Domains</a></li>
                        <li class="{{Style::set_active_starts_with('site')}}"><a href="{{url('site')}}"><i
                                        class="fa fa-circle-o"></i> Sites</a></li>
                    </ul>
                </li>
                <li class="treeview {{Style::set_active_starts_with('um.')}}">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span>User Management</span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{Style::set_active_starts_with('um.user')}}"><a
                                    href="{{route('um.user.index')}}"><i
                                        class="fa fa-user"></i> Users</a></li>
                        <li class="{{Style::set_active_starts_with('um.group')}}"><a
                                    href="{{route('um.group.index')}}"><i
                                        class="fa fa-users"></i> Groups</a></li>
                        <li class="{{Style::set_active_starts_with('um.role')}}"><a
                                    href="{{route('um.role.index')}}"><i
                                        class="fa fa-tags"></i> Roles</a></li>
                        <li class="{{Style::set_active_starts_with('um.permission')}}"><a
                                    href="{{route('um.permission.index')}}">
                                <i class="fa fa-key"></i> Permissions</a></li>
                    </ul>
                </li>
                <li class="treeview {{Style::set_active_starts_with('log.')}}">
                    <a href="#">
                        <i class="fa fa-file-text-o"></i>
                        <span>System Log Management</span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{Style::set_active_starts_with('log.user_activity')}}">
                            <a href="{{route('log.user_activity.index')}}">
                                <i class="fa fa-map-o"></i> User Activity Logs
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>