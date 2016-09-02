<header class="main-header">
    <!-- Logo -->
    <a href="../../index2.html" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img src="{{asset('build/images/logo-fixed.png')}}" alt="SpotLite" height="50"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            <img src="{{asset('build/images/logo-fixed.png')}}" alt="SpotLite" height="50">
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @if(Auth::check())
                    <li class="dropdown spotlite-user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="hidden-xs">
                                <i class="fa fa-user"></i>&nbsp;
                                {{Auth::user()->first_name}} {{Auth::user()->last_name}}
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <ul class="spotlite-menu">
                                    <li>
                                        <a href="{{route('profile.index')}}">
                                            <h3>
                                                Profile
                                            </h3>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{route('account.index')}}">
                                            <h3>
                                                Account Settings
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
                                    <li>
                                        <a href="{{route('logout')}}">
                                            <h3>
                                                Logout
                                            </h3>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>