<header class="main-header">
    <!-- Logo -->
    <a href="{{route('dashboard.index')}}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img src="{{asset('build/images/favicon.png')}}" alt="SpotLite" height="30"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
{{--            <img src="{{asset('build/images/logo-fixed-custom.png')}}" alt="SpotLite" height="40">--}}
            {{--            <img src="{{asset('build/images/SpotLite_Logo_with_tagline2.png')}}" alt="SpotLite" height="100">--}}

            <img src="{{asset('build/images/logo_transparent_white_text.png')}}" alt="SpotLite" height="75"
                 style="padding-top: 20px;">
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle visible-xs" data-toggle="offcanvas" role="button"
           onclick="saveSidebarStatus()">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @if(Auth::check())
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-question-circle"></i>
                            &nbsp;NEED HELP ?
                            &nbsp;&nbsp;&nbsp;
                            <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="https://spotlitehelp.zendesk.com/hc/en-us/categories/204664368-FAQ" target="_blank">FAQ</a></li>
                            <li><a href="#">Tutorials</a></li>
                            <li><a href="#">Contact us</a></li>
                        </ul>
                    </li>
                    <li class="spotlite-user-menu">
                        <a href="{{route('account.index')}}">
                            <i class="fa fa-wrench"></i>&nbsp;
                            <span class="hidden-xs">
                                ACCOUNT SETTINGS
                            </span>&nbsp;
                        </a>
                    </li>
                    <li>
                        <a href="{{route('logout')}}" onclick="gaLogout();">
                            <i class="fa fa-sign-in"></i>&nbsp;
                            SIGN OUT
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>