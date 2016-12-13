<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            @if(auth()->check() && (auth()->user()->isStaff || (!is_null(auth()->user()->subscription) && auth()->user()->subscription->isValid())))
                <li class="treeview {{Style::set_active('/')}} {{Style::set_active_starts_with('dashboard')}}">
                    <a href="#">
                        <i class="fa fa-dashboard"></i>
                        <span>DASHBOARDS</span>
                        <span class="pull-right-container">
                          <i class="fa fa-caret-down pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @foreach(auth()->user()->nonHiddenDashboards as $index=>$dashboard)
                            <li class="{{Style::set_active_and(array('dashboard', $dashboard->getKey()))}}">
                                <a href="{{route('dashboard.show', $dashboard->getKey())}}" class="lnk-dashboard">
                                    <i class="fa fa-circle-o"></i>
                                    <span class="lnk-dashboard-{{$dashboard->getKey()}}">
                                        {{$dashboard->dashboard_name}}
                                    </span>
                                    @if(auth()->user()->nonHiddenDashboards()->count() > 1)
                                        @if($index==0)
                                            <span class="pull-right-container btn-reorder-dashboard" data-order="{{$index}}"
                                                  data-dashboard-id="{{$dashboard->getKey()}}"
                                                  onclick="swapDashboard(this); event.preventDefault(); event.stopPropagation(); return false;">
                                                <i class="fa fa-arrow-down"></i>
                                            </span>
                                        @else
                                            <span class="pull-right-container btn-reorder-dashboard" data-order="{{$index}}"
                                                  data-dashboard-id="{{$dashboard->getKey()}}"
                                                  onclick="swapDashboard(this); event.preventDefault(); event.stopPropagation(); return false;">
                                                <i class="fa fa-arrow-up"></i>
                                            </span>
                                        @endif
                                    @endif
                                </a>
                            </li>
                        @endforeach
                        <script type="text/javascript">
                        </script>
                    </ul>
                </li>
                <li class="bg-fff">
                    <a href="#" onclick="showAddDashboardForm(this); return false;">
                        <i class="fa fa-plus"></i>
                        <span>ADD A NEW DASHBOARD</span>
                    </a>
                </li>
                <li class="{{Style::set_active_starts_with('product')}}">
                    <a href="{{url('product')}}">
                        <i class="fa fa-tag"></i>
                        <span>PRODUCTS</span>
                    </a>
                </li>
                <li class="{{Style::set_active('alert')}}">
                    <a href="{{url('alert')}}">
                        <i class="fa fa-bell-o"></i>
                        <span>ALERTS</span></a>
                </li>
                <li class="{{Style::set_active('report')}}">
                    <a href="{{url('report')}}">
                        <i class="fa fa-line-chart"></i>
                        <span>REPORTS</span>
                    </a>
                </li>
            @endif
            @if(auth()->check() && auth()->user()->isStaff)
                <li class="{{Style::set_active_and(array('admin', 'app_preference'))}}">
                    <a href="{{route("admin.app_preference.index")}}">
                        <i class="fa fa-gears"></i>
                        <span>APP PREFERENCES</span>
                    </a>
                </li>
                <li class="treeview {{Style::set_active_and(array('admin', 'site'))}} {{Style::set_active_and(array('admin', 'domain'))}}">
                    <a href="#">
                        <i class="fa fa-files-o"></i>
                        <span>CRAWLER MANAGEMENT</span>
                        <span class="pull-right-container">
                          <i class="fa fa-caret-down pull-right"></i>
                        </span>
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
                        <span>USER MANAGEMENT</span>
                        <span class="pull-right-container">
                          <i class="fa fa-caret-down pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{Style::set_active_starts_with('um.user')}}">
                            <a href="{{route('um.user.index')}}">
                                <i class="fa fa-user"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li class="{{Style::set_active_starts_with('um.group')}}">
                            <a href="{{route('um.group.index')}}">
                                <i class="fa fa-users"></i>
                                <span>Groups</span>
                            </a>
                        </li>
                        <li class="{{Style::set_active_starts_with('um.role')}}">
                            <a href="{{route('um.role.index')}}">
                                <i class="fa fa-tags"></i>
                                <span>Roles</span>
                            </a>
                        </li>
                        <li class="{{Style::set_active_starts_with('um.permission')}}">
                            <a href="{{route('um.permission.index')}}">
                                <i class="fa fa-key"></i>
                                <span>Permissions</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="treeview {{Style::set_active_starts_with('log.')}}">
                    <a href="#">
                        <i class="fa fa-file-text-o"></i>
                        <span>SYSTEM LOG MANAGEMENT</span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{Style::set_active_starts_with('log.crawler_activity')}}">
                            <a href="{{route('log.crawler_activity.index')}}">
                                <i class="fa fa-gear"></i>
                                <span>Crawler Logs</span>
                            </a>
                        </li>
                        <li class="{{Style::set_active_starts_with('log.user_activity')}}">
                            <a href="{{route('log.user_activity.index')}}">
                                <i class="fa fa-map-o"></i>
                                <span>User Activity Logs</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

@section('scripts')
@stop