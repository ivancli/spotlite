<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            @if(auth()->check() && (auth()->user()->hasValidSubscription() || auth()->user()->isStaff()))
                <li class="treeview {{Style::set_active('/')}} {{Style::set_active_starts_with('dashboard')}}">
                    <a href="#">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                    <ul class="treeview-menu">
                        @foreach(auth()->user()->nonHiddenDashboard as $dashboard)
                            <li class="{{Style::set_active_and(array('dashboard', $dashboard->getKey()))}}">
                                <a href="{{route('dashboard.show', $dashboard->getKey())}}">
                                    <i class="fa fa-circle-o"></i> {{$dashboard->dashboard_name}}
                                </a>
                            </li>
                        @endforeach
                        <li class="divider"></li>
                        <li class="{{Style::set_active_and(array('dashboard', 'manage'))}}">
                            <a href="{{route('dashboard.manage')}}">
                                <i class="fa fa-gear"></i> Manage Dashboards
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="{{Style::set_active('report')}}"><a href="{{url('report')}}"><i
                                class="fa fa-line-chart"></i> Reports</a></li>
                <li class="{{Style::set_active('alert')}}"><a href="{{url('alert')}}"><i
                                class="fa fa-bell-o"></i> Alerts</a></li>
                <li class="{{Style::set_active_starts_with('product')}}"><a href="{{url('product')}}"><i
                                class="fa fa-square-o"></i> <span>Products</span></a></li>
                {{--<li class="treeview {{Style::set_active_or(array('report', 'alert'))}}">--}}
                {{--<a href="#">--}}
                {{--<i class="fa fa-envelope"></i>--}}
                {{--<span>Reports and Alerts</span>--}}
                {{--</a>--}}
                {{--<ul class="treeview-menu">--}}
                {{--<li class="{{Style::set_active('report')}}"><a href="{{url('report')}}"><i--}}
                {{--class="fa fa-line-chart"></i> Reports</a></li>--}}
                {{--<li class="{{Style::set_active('alert')}}"><a href="{{url('alert')}}"><i--}}
                {{--class="fa fa-bell-o"></i> Alerts</a></li>--}}
                {{--</ul>--}}
                {{--</li>--}}

                {{--<li class="treeview {{Style::set_active_starts_with(array('group.'))}}">--}}
                {{--<a href="#">--}}
                {{--<i class="fa fa-users"></i>--}}
                {{--<span>Group Management</span>--}}
                {{--</a>--}}
                {{--<ul class="treeview-menu">--}}
                {{--<li class="{{Style::set_active('dashboard')}}"><a href="{{url('alert')}}"><i--}}
                {{--class="fa fa-bell-o"></i> Alerts</a></li>--}}
                {{--</ul>--}}
                {{--</li>--}}
            @endif
            @if(auth()->check() && auth()->user()->isStaff())
                <li class="{{Style::set_active_and(array('admin', 'app_preference'))}}">
                    <a href="{{route("admin.app_preference.index")}}"><i class="fa fa-gears"></i>
                        <span>App Preferences</span>
                    </a>
                </li>
                <li class="treeview {{Style::set_active_and(array('admin', 'site'))}} {{Style::set_active_and(array('admin', 'domain'))}}">
                    <a href="#">
                        <i class="fa fa-files-o"></i>
                        <span>Crawler Management</span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{Style::set_active_and(array('admin', 'domain'))}}">
                            <a href="{{route('admin.domain.index')}}">
                                <i class="fa fa-circle-o"></i> Domains
                            </a>
                        </li>
                        <li class="{{Style::set_active_and(array('admin', 'site'))}}">
                            <a href="{{route('admin.site.index')}}">
                                <i class="fa fa-circle-o"></i> Sites
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="treeview {{Style::set_active_starts_with('um.')}}">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span>User Management</span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{Style::set_active_starts_with('um.user')}}">
                            <a href="{{route('um.user.index')}}">
                                <i class="fa fa-user"></i> Users
                            </a>
                        </li>
                        <li class="{{Style::set_active_starts_with('um.group')}}">
                            <a href="{{route('um.group.index')}}">
                                <i class="fa fa-users"></i> Groups
                            </a>
                        </li>
                        <li class="{{Style::set_active_starts_with('um.role')}}">
                            <a href="{{route('um.role.index')}}">
                                <i class="fa fa-tags"></i> Roles
                            </a>
                        </li>
                        <li class="{{Style::set_active_starts_with('um.permission')}}">
                            <a href="{{route('um.permission.index')}}">
                                <i class="fa fa-key"></i> Permissions
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="treeview {{Style::set_active_starts_with('log.')}}">
                    <a href="#">
                        <i class="fa fa-file-text-o"></i>
                        <span>System Log Management</span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{Style::set_active_starts_with('log.crawler_activity')}}">
                            <a href="{{route('log.crawler_activity.index')}}">
                                <i class="fa fa-gear"></i> Crawler Logs
                            </a>
                        </li>
                        <li class="{{Style::set_active_starts_with('log.user_activity')}}">
                            <a href="{{route('log.user_activity.index')}}">
                                <i class="fa fa-map-o"></i> User Activity Logs
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
        {{--@if(auth()->check() && auth()->user()->hasValidSubscription() && starts_with(Request::route()->getName(), 'product'))--}}
        {{--<hr class="sidebar-divider">--}}
        {{--<div class="subscription-panel">--}}
        {{--<div class="text-center">--}}
        {{--My Plan: {{auth()->user()->cachedAPISubscription()->product->name}}--}}
        {{--</div>--}}
        {{--<div class="block-button-container">--}}
        {{--<a href="{{route('subscription.edit', auth()->user()->validSubscription()->getKey())}}" class="btn btn-block btn-success">--}}
        {{--UPGRADE--}}
        {{--</a>--}}
        {{--</div>--}}
        {{--<div class="text-center">--}}
        {{--<a href="#" style="text-decoration: underline;">--}}
        {{--Need Help?--}}
        {{--</a>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--@endif--}}
    </section>
    <!-- /.sidebar -->
</aside>

@section('scripts')
@stop