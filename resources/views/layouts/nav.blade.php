<!-- BEGIN: SideNav -->
@php
$isMenuDark       = '';
$activeMenuColor  = '';
$menuCollapsed    = '';
$navLock          = '';
$navCollapsed     = '';
$menuStyle        = '';


if (!empty($themeSettings)) {
  $activeMenuColor  = ($themeSettings->activeMenuColor != '')?$themeSettings->activeMenuColor.' gradient-shadow':$configData['activeMenuColor'];
  $isMenuDark       = ($themeSettings->isMenuDark == 0)?'sidenav-light':'sidenav-dark';
  $navLock          = ($themeSettings->menuCollapsed == 1)?'':'nav-lock';
  $navCollapsed     = ($themeSettings->menuCollapsed == 1)?'nav-collapsed':'';
  $menuStyle        = $themeSettings->menuStyle;
} else {
  $isMenuDark       = $configData['sidenavMainColor'];
  $navbarBgColor    = $configData['navbarLargeColor'];
  $menuStyle        = 'sidenav-active-square';
}
@endphp

    @if(Auth::user()->is_admin == 1)
      <aside class="sidenav-main nav-expanded nav-lock nav-collapsible sidenav-light sidenav-active-square">
    @else
      <aside class="sidenav-main nav-expanded {{$navLock}} nav-collapsible {{$navCollapsed}} {{$isMenuDark}} {{$menuStyle}}">
    @endif
      <div class="brand-sidebar">
        <h1 class="logo-wrapper">
          <a class="brand-logo darken-1" href="{{ url('home/') }}">
            <img class="hide-on-med-and-down" src="{{asset('admin/images/logo/logo.png')}}" alt="materialize logo"/>
            <img class="show-on-medium-and-down hide-on-med-and-up" src="{{asset('admin/images/logo/logo.png')}}" alt="materialize logo"/>
            <span class="logo-text hide-on-med-and-down">Billbae</span>
          </a>
          <a class="navbar-toggler" href="javascript:"> <i class="material-icons">radio_button_checked</i> </a>
        </h1>
      </div>
      <ul class="sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow" id="slide-out" data-menu="menu-navigation" data-collapsible="menu-accordion">
          <li class="bold"><a class="@if(Request::is(ROUTE_PREFIX.'home')) active {{$activeMenuColor}} @endif waves-effect waves-cyan" href="{{ url(ROUTE_PREFIX.'/home') }}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Dashboard</span></a>               
          </li>
        @if(Auth::user()->is_admin == 1)
          <li class="bold"><a class="@if (Request::is(ROUTE_PREFIX.'stores*') ||  Request::is(ROUTE_PREFIX.'stores/create*')) active {{$activeMenuColor}} @endif waves-effect waves-cyan " href="{{ url(ROUTE_PREFIX.'/stores') }}"><i class="material-icons">business</i><span class="menu-title" data-i18n="Stores">Stores</span></a>
          </li>
          <li class="bold"><a class="@if (Request::is(ROUTE_PREFIX.'roles*') ||  Request::is(ROUTE_PREFIX.'roles/create*')) active {{$activeMenuColor}} @endif waves-effect waves-cyan " href="{{ url(ROUTE_PREFIX.'/roles') }}"><i class="material-icons">settings</i><span class="menu-title" data-i18n="Stores">Roles</span></a>
          </li>
          <li class="bold"><a class="@if (Request::is(ROUTE_PREFIX.'notifications*') ||  Request::is(ROUTE_PREFIX.'roles/notifications*')) active {{$activeMenuColor}} @endif waves-effect waves-cyan " href="javascript:"><i class="material-icons">notifications</i><span class="menu-title" data-i18n="Stores">Notifications</span></a>
          </li>
        @else
          <li class="bold"><a class="@if (Request::is(ROUTE_PREFIX.'schedules*') ||  Request::is(ROUTE_PREFIX.'schedules/create*')) active {{$activeMenuColor}} @endif waves-effect waves-cyan " href="{{ url(ROUTE_PREFIX.'/schedules') }}"><i class="material-icons">schedule</i><span class="menu-title" data-i18n="Billing">Schedule</span></a>
          </li>
          <li class="bold"><a class="@if (Request::is(ROUTE_PREFIX.'billings*') ||  Request::is(ROUTE_PREFIX.'billings/create*')) active {{$activeMenuColor}} @endif waves-effect waves-cyan " href="{{ url(ROUTE_PREFIX.'/billings') }}"><i class="material-icons">receipt</i><span class="menu-title" data-i18n="Billing">Billing</span></a>
          </li>
          <li class="bold"><a class="@if (Request::is(ROUTE_PREFIX.'cashbook*')) active {{$activeMenuColor}} @endif waves-effect waves-cyan " href="{{ url(ROUTE_PREFIX.'/cashbook') }}"><i class="material-icons">account_balance</i><span class="menu-title" data-i18n="Cashbook">Cashbook</span></a>
          </li>
          <li class="bold"><a class="@if (Request::is(ROUTE_PREFIX.'customers*') ||  Request::is(ROUTE_PREFIX.'customers/create*') || Request::is(ROUTE_PREFIX.'customers/create')) active {{$activeMenuColor}} @endif waves-effect waves-cyan " href="{{ url(ROUTE_PREFIX.'/customers') }}"><i class="material-icons">people</i><span class="menu-title" data-i18n="Customers">Customers</span></a>
          </li>
          <li class="bold"><a class="@if (Request::is(ROUTE_PREFIX.'reports*') ||  Request::is(ROUTE_PREFIX.'reports/sales-report*')) active {{$activeMenuColor}} @endif waves-effect waves-cyan " href="{{ url(ROUTE_PREFIX.'/reports/sales-report') }}"><i class="material-icons">report</i><span class="menu-title" data-i18n="Reports">Reports</span></a>
          </li>
        @endif
        <!-- <li class="navigation-header"><a class="navigation-header-text">Tables &amp; Forms </a><i class="navigation-header-icon material-icons">more_horiz</i></li>
        <li class="bold"><a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)"><i class="material-icons">chrome_reader_mode</i><span class="menu-title" data-i18n="Forms">Forms</span></a>
          <div class="collapsible-body">
            <ul class="collapsible collapsible-sub" data-collapsible="accordion">
              <li><a href="form-elements.html"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Form Elements">Form Elements</span></a>
              </li>
              <li><a href="form-select2.html"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Form Select2">Form Select2</span></a>
              </li>
              <li><a href="form-validation.html"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Form Validation">Form Validation</span></a>
              </li>
              <li><a href="form-masks.html"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Form Masks">Form Masks</span></a>
              </li>
              <li><a href="form-editor.html"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Form Editor">Form Editor</span></a>
              </li>
              <li><a href="form-file-uploads.html"><i class="material-icons">radio_button_unchecked</i><span data-i18n="File Uploads">File Uploads</span></a>
              </li>
            </ul>
          </div>
        </li> -->
      </ul>
      <div class="navigation-background"></div><a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only" href="#" data-target="slide-out"><i class="material-icons">menu</i></a>
    </aside>
    <!-- END: SideNav-->