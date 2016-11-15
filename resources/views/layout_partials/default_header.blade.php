<header class="main-header">
    <!-- Logo -->
    <a href="{{route('dashboard.index')}}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img src="{{asset('images/favicon.png')}}" alt="SpotLite" height="30"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            <img src="{{asset('images/logo-fixed-custom.png')}}" alt="SpotLite" height="40">
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" onclick="saveSidebarStatus()">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <a href="#" class="header-label" onclick="return false;" style="color: black;">
            Focus on what matters
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @if(Auth::check())
                    <li class="dropdown spotlite-user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>&nbsp;
                            <span class="hidden-xs">
                                {{Auth::user()->first_name}} {{Auth::user()->last_name}}
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <ul class="spotlite-menu">
                                    @if(auth()->user()->subscription->isValid() || auth()->user()->isStaff())
                                        <li>
                                            <a href="{{route('account.index')}}">
                                                <h3>
                                                    Settings
                                                </h3>
                                            </a>
                                        </li>
                                        @if(!auth()->user()->isStaff())
                                            <li>
                                                <a href="{{route('subscription.index')}}">
                                                    <h3>
                                                        Manage My Subscription
                                                    </h3>
                                                </a>
                                            </li>
                                        @endif
                                    @endif
                                    <li>
                                        <a href="{{route('logout')}}" onclick="gaLogout();">
                                            <h3>
                                                Logout
                                            </h3>
                                        </a>
                                    </li>
                                    @if(auth()->user()->subscription->isValid() && !auth()->user()->isStaff())
                                        <li>
                                            <div style="padding: 2px;">

                                                <a class="btn btn-success btn-block btn-flat" href="{{route('subscription.edit', auth()->user()->subscription->getKey())}}">
                                                        UPGRADE
                                                </a>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>