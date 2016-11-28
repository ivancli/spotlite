<header class="main-header">
    <!-- Logo -->
    <a href="{{route('dashboard.index')}}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img src="{{asset('build/images/favicon.png')}}" alt="SpotLite" height="30"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
{{--            <img src="{{asset('build/images/logo-fixed-custom.png')}}" alt="SpotLite" height="40">--}}
            <img src="{{asset('build/images/SpotLite_Logo_with_tagline2.png')}}" alt="SpotLite" height="100">
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" onclick="saveSidebarStatus()">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @if(Auth::check())
                    <li>
                        <a href="https://spotlitehelp.zendesk.com/hc/en-us" target="_blank">Need Help?</a>
                    </li>
                    <li class="spotlite-user-menu">
                        <a href="{{route('account.index')}}">
                            <i class="fa fa-wrench"></i>&nbsp;
                            <span class="hidden-xs">
                                Account Settings
                            </span>&nbsp;
                        </a>
                        {{--<ul class="dropdown-menu">--}}
                            {{--<li>--}}
                                {{--<ul class="spotlite-menu">--}}
                                    {{--@if(auth()->user()->isStaff || (!is_null(auth()->user()->subscription) && auth()->user()->subscription->isValid()))--}}
                                        {{--<li>--}}
                                            {{--<a href="{{route('account.index')}}">--}}
                                                {{--<h3>--}}
                                                    {{--Settings--}}
                                                {{--</h3>--}}
                                            {{--</a>--}}
                                        {{--</li>--}}
                                        {{--@if(!auth()->user()->isStaff)--}}
                                            {{--<li>--}}
                                                {{--<a href="{{route('subscription.index')}}">--}}
                                                    {{--<h3>--}}
                                                        {{--Manage My Subscription--}}
                                                    {{--</h3>--}}
                                                {{--</a>--}}
                                            {{--</li>--}}
                                        {{--@endif--}}
                                    {{--@endif--}}
                                    {{--@if(!auth()->user()->isStaff && !is_null(auth()->user()->subscription) && auth()->user()->subscription->isValid())--}}
                                        {{--<li>--}}
                                            {{--<div style="padding: 2px;">--}}

                                                {{--<a class="btn btn-success btn-block btn-flat"--}}
                                                   {{--href="{{route('subscription.edit', auth()->user()->subscription->getKey())}}">--}}
                                                    {{--UPGRADE--}}
                                                {{--</a>--}}
                                            {{--</div>--}}
                                        {{--</li>--}}
                                    {{--@endif--}}
                                {{--</ul>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    </li>
                    <li>
                        <a href="{{route('logout')}}" onclick="gaLogout();">
                            <i class="fa fa-sign-in"></i>&nbsp;
                            Sign Out
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>