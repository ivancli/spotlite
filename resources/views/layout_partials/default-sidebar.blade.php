<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{url('product')}}"><i class="fa fa-square-o"></i> <span>Products</span></a></li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-envelope"></i>
                    <span>Reports and Alerts</span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{url('report')}}"><i class="fa fa-line-chart"></i> Reports</a></li>
                    <li><a href="{{url('alert')}}"><i class="fa fa-bell-o"></i> Alerts</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-files-o"></i>
                    <span>Crawler Management</span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{url('domain')}}"><i class="fa fa-circle-o"></i> Domains</a></li>
                    <li><a href="{{url('site')}}"><i class="fa fa-circle-o"></i> Sites</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-files-o"></i>
                    <span>User Management</span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{url('user')}}"><i class="fa fa-circle-o"></i> Users</a></li>
                    <li><a href="{{url('group')}}"><i class="fa fa-circle-o"></i> Groups</a></li>
                    <li><a href="{{url('role')}}"><i class="fa fa-circle-o"></i> Roles</a></li>
                    <li><a href="{{url('permission')}}"><i class="fa fa-circle-o"></i> Permissions</a></li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>