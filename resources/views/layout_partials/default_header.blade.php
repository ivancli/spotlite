<header class="main-header">
    <nav class="navbar navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <a href="{{url('/')}}" class="navbar-brand">
                    <img src="{{asset('build/images/favicon.png')}}" alt="SpotLite" style="max-height: 40px;">
                </a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            @if(auth()->check())
            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="{{Style::set_active('/')}} {{Style::set_active_starts_with('dashboard')}}">
                        <a href="{{route('dashboard.index')}}">
                            <i class="fa fa-dashboard"></i>&nbsp;DASHBOARDS
                        </a>
                    </li>
                    <li class="{{Style::set_active_starts_with('product')}}">
                        <a href="{{route('product.index')}}">
                            <i class="fa fa-tag"></i>&nbsp;PRODUCTS
                        </a>
                    </li>
                    <li class="{{Style::set_active_starts_with('positioning')}}">
                        <a href="{{route('positioning.index')}}">
                            <i class="fa fa-street-view"></i>&nbsp;POSITIONING
                        </a>
                    </li>
                    <li class="{{Style::set_active('alert')}}">
                        <a href="{{route('alert.index')}}">
                            <i class="fa fa-bell-o"></i>&nbsp;ALERTS
                        </a>
                    </li>
                    <li class="{{Style::set_active('report')}}">
                        <a href="{{route('report.index')}}">
                            <i class="fa fa-envelope-o"></i>&nbsp;REPORTS
                        </a>
                    </li>
                    @if(auth()->check() && auth()->user()->isStaff)
                        @if(auth()->user()->can('manage_app_preference'))
                            <li class="{{Style::set_active_and(array('admin', 'app_preference'))}}">
                                <a href="{{route("admin.app_preference.index")}}">
                                    <i class="fa fa-gears"></i>
                                    <span class="hidden-lg hidden-md hidden-sm">App Preferences</span>
                                </a>
                            </li>
                        @endif
                        <li class="dropdown {{Style::set_active_and(array('admin', 'site'))}} {{Style::set_active_and(array('admin', 'domain'))}}">
                            <a href="#" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-files-o"></i>&nbsp;<span class="hidden-lg hidden-md hidden-sm">Manage Crawler</span>&nbsp;<i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                @if(auth()->user()->can(['manage_admin_domain', 'read_admin_domain', 'create_admin_domain', 'update_admin_domain_preference', 'delete_admin_domain']))
                                    <li class="{{Style::set_active_and(array('admin', 'domain'))}}">
                                        <a href="{{route('admin.domain.index')}}">
                                            <i class="fa fa-circle-o"></i> Domains
                                        </a>
                                    </li>
                                @endif
                                @if(auth()->user()->can(['read_admin_site', 'create_admin_site', 'delete_admin_site', 'update_admin_site_status', 'update_admin_site_preference', 'test_admin_site', 'manage_admin_site']))
                                    <li class="{{Style::set_active_and(array('admin', 'site'))}}">
                                        <a href="{{route('admin.site.index')}}">
                                            <i class="fa fa-circle-o"></i> Sites
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        @if(auth()->user()->can('manage_user'))
                            <li class="dropdown {{Style::set_active_starts_with('um.')}}">
                                <a href="#" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-users"></i>&nbsp;<span class="hidden-lg hidden-md hidden-sm">Manage Users</span>&nbsp;<i class="fa fa-caret-down"></i>
                                </a>
                                <ul class="dropdown-menu">
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
                        @endif
                        <li class="dropdown {{Style::set_active_starts_with('log.')}}">
                            <a href="#" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-file-text-o"></i>&nbsp;<span class="hidden-lg hidden-md hidden-sm">System Logs</span>&nbsp;<i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                @if(auth()->user()->can('read_crawler_log'))
                                    <li class="{{Style::set_active_starts_with('log.crawler_activity')}}">
                                        <a href="{{route('log.crawler_activity.index')}}">
                                            <i class="fa fa-gear"></i>
                                            <span>Crawler Logs</span>
                                        </a>
                                    </li>
                                @endif
                                @if(auth()->user()->can('read_user_activity_log'))
                                    <li class="{{Style::set_active_starts_with('log.user_activity')}}">
                                        <a href="{{route('log.user_activity.index')}}">
                                            <i class="fa fa-map-o"></i>
                                            <span>User Activity Logs</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        @if(auth()->user()->can(['manage_terms_and_conditions', 'manage_privacy_policies']))
                            <li class="dropdown {{Style::set_active_starts_with('term_and_condition')}} {{Style::set_active_starts_with('privacy_policy')}}">
                                <a href="#" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-file-archive-o"></i>&nbsp;<span class="hidden-lg hidden-md hidden-sm">Manage Legals</span>&nbsp;<i class="fa fa-caret-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="{{Style::set_active_starts_with('term_and_condition')}}">
                                        <a href="{{route('term_and_condition.index')}}">
                                            <i class="fa fa-square"></i>
                                            <span>Terms and Conditions</span>
                                        </a>
                                    </li>
                                    <li class="{{Style::set_active_starts_with('privacy_policy')}}">
                                        <a href="{{route('privacy_policy.index')}}">
                                            <i class="fa fa-square"></i>
                                            <span>Privacy Policies</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    @endif
                </ul>
                @if(request()->route()->getName() == "product.index")
                    <form class="navbar-form navbar-left" role="search" onsubmit="return false;">
                        <div class="form-group">
                            <input type="text" class="form-control general-search-input" id="navbar-search-input" placeholder="SEARCH">
                        </div>
                    </form>
                @endif
            </div>
            @endif
            <!-- /.navbar-collapse -->
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    @if(Auth::check())
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle lnk-drop-down-need-help" data-toggle="dropdown"
                               aria-expanded="false">
                                <i class="glyphicon glyphicon-cog"></i>&nbsp;<i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="{{route('account.index')}}#user-settings">My Account</a></li>
                                <li><a href="{{route('account.index')}}#import-products">Bulk Import <span style="font-size: 10px;color: #00d200;font-weight: bold;position: absolute;padding-left: 3px;">NEW</span></a></li>
                                <li><a href="{{route('account.index')}}#user-domains">Site Names</a></li>
                                <li><a href="{{route('account.index')}}#user-password">Reset Password</a></li>
                                {{--<li><a href="{{route('account.index')}}#display-settings">Display Settings</a></li>--}}
                                @if(auth()->user()->needSubscription)
                                    <li><a href="{{route('account.index')}}#manage-subscription">Manage My Subscription</a></li>
                                @endif
                                <li><a href="{{route('logout')}}" onclick="gaLogout();">Logout</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle lnk-drop-down-need-help" data-toggle="dropdown"
                               aria-expanded="false">
                                <i class="fa fa-question-circle"></i>&nbsp;<i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#" onclick="startSpotLiteTour(this); return false;">
                                        SpotLite Tour
                                    </a>
                                </li>
                                <li>
                                    <a href="https://spotlitehelp.zendesk.com/hc/en-us/categories/204682247-Video-Tutorials"
                                       target="_blank">Video
                                        Tutorials</a></li>
                                <li><a href="https://spotlitehelp.zendesk.com/hc/en-us/categories/204664368-FAQ"
                                       target="_blank">FAQ</a></li>
                                <li>
                                    <a href="https://spotlitehelp.zendesk.com/hc/en-us/categories/204682187-Step-by-Step-Guide"
                                       target="_blank">Step by Step Guide</a>
                                </li>
                                <li><a href="#" onclick="showContactUs(); return false;">Contact us</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- /.navbar-custom-menu -->
        </div>
        <!-- /.container-fluid -->
    </nav>
</header>
