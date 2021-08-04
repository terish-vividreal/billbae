    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url(ROUTE_PREFIX.'/home') }}" class="brand-link">
      <img src="{{ asset('admin/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('admin/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="{{ url(ROUTE_PREFIX.'/home') }}" class="d-block">{{ Auth::user()->name }}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <!-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class  with font-awesome or any other icon font library -->
            <li class="nav-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}" class="nav-link {{ Request::is('home*') ? 'active' : '' }}"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a> </li>

            @php $role = (Auth::user()->is_admin == 1) ? 'admin/' : '' ; @endphp

            @if(Auth::user()->is_admin == 1)
                <li class="nav-item {{ Request::is('admin/stores*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::is('admin/stores*') ? 'active' : '' }}"><i class="nav-icon fas fa-user"></i><p>Manage Stores <i class="fas fa-angle-left right"></i></p></a>
                    <ul class="nav nav-treeview">                
                        @can('user-create')
                        <li class="nav-item">
                            <a href="{{ url(ROUTE_PREFIX.'/stores/create') }}" class="nav-link {{ Request::is('admin/stores/create*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p> Add New </p></a>
                        </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ url(ROUTE_PREFIX.'/stores') }}" class="nav-link {{ Request::is('admin/stores') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>List All</p></a>
                        </li>
                    </ul>
                </li>
            @else
              @can('user-list')         
                <li class="nav-item {{ Request::is('users*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::is('users*') ? 'active' : '' }}"><i class="nav-icon fas fa-user"></i><p> Staffs <i class="fas fa-angle-left right"></i></p></a>
                    <ul class="nav nav-treeview">             
                        @can('user-create')
                        <li class="nav-item">
                            <a href="{{ url(ROUTE_PREFIX.'/users/create') }}" class="nav-link {{ Request::is('users/create*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p> Add New </p></a>
                        </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ url(ROUTE_PREFIX.'/users') }}" class="nav-link {{ Request::is('users') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>List All</p></a>
                        </li>
                    </ul>
                </li>
              @endcan

            @endif

            @can('manage-business-types')
              <li class="nav-item">
                  <a href="{{ url(ROUTE_PREFIX.'/business-types') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'/business-types*') ? 'active' : '' }} "><i class="nav-icon fas fa fa-briefcase"></i><p>Business Types</p></a>
              </li>            
            @endcan

            <li class="nav-item {{ Request::is(ROUTE_PREFIX.'customers*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is(ROUTE_PREFIX.'customers*') ? 'active' : '' }}"><i class="nav-icon fas fa fa-user-plus"></i><p>Customers <i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/customers/create') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'customers/create*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Add New </p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/customers') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'customers') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>List All</p></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item @if ( Request::is(ROUTE_PREFIX.'billings*')) ||  Request::is(Request::is(ROUTE_PREFIX.'payment-types')) ) menu-open @endif">
                        
                
                <a href="#" class="nav-link {{ Request::is(ROUTE_PREFIX.'billings*') ? 'active' : '' }}"><i class="nav-icon fas fa fa-book"></i><p>Billing <i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/billings/create') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'billings/create*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Add New </p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/billings') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'billings') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>List All</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/payment-types') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'payment-types') ? 'active' : '' }}"><i class="far fa-plus-square nav-icon"></i><p>Payment Types</p></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item @if (Request::is('services*') ) menu-open  @endif">
                <a href="#" class="nav-link {{ Request::is('services*') ? 'active' : '' }}"><i class="nav-icon fas fa-list-ul"></i><p> Services <i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview"> 
                    @can('user-create')
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/services/create') }}" class="nav-link {{ Request::is('services/create') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p> Add New </p></a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/services') }}" class="nav-link {{ Request::is('services') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>List All</p></a>
                    </li>                    
                </ul>
            </li>

            <li class="nav-item {{ Request::is(ROUTE_PREFIX.'packages*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is(ROUTE_PREFIX.'packages*') ? 'active' : '' }}"><i class="nav-icon fas fa-briefcase"></i><p>Packages <i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/packages/create') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'packages/create*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Add New </p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/packages') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'packages') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>List All</p></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item"><a href="{{ url(ROUTE_PREFIX.'/additional-tax') }}" class="nav-link {{ Request::is('additional-tax*') ? 'active' : '' }}"><i class="nav-icon fas fa fa-tags "></i><p>Additional tax</p></a> </li>
            
            
            @can('service-category-edit', 'service-category-create', 'service-category-delete')
            <li class="nav-item"> <a href="{{ url(ROUTE_PREFIX.'/service-category') }}" class="nav-link {{ Request::is('service-category') ? 'active' : '' }}"><i class="nav-icon fa fa-forward"></i><p>Service category</p></a></li>
            @endcan

            <li class="nav-item {{ Request::is(ROUTE_PREFIX.'reports*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is(ROUTE_PREFIX.'reports*') ? 'active' : '' }}"><i class="nav-icon fas fa-file"></i><p>Reports <i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/reports/sales-report') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'reports/sales-report*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Sales Report </p></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ Request::is(ROUTE_PREFIX.'cashbook*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is(ROUTE_PREFIX.'cashbook*') ? 'active' : '' }}"><i class="nav-icon fa fa-book"></i><p>Cash book <i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/cashbook') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'cashbook*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>List </p></a>
                    </li>
                </ul>
            </li>

            @can('manage-location')
              <li class="nav-item @if (Request::is('country*') ||  Request::is('states*') ||  Request::is('districts*')) menu-open @endif">
                  <a href="#" class="nav-link @if (Request::is('country*') ||  Request::is('states*') ||  Request::is('districts*')) active @endif"><i class="nav-icon fa fa-globe" ></i><p>Location <i class="fas fa-angle-left right"></i></p></a>
                  <ul class="nav nav-treeview">                    
                      <li class="nav-item">
                          <a href="{{ url(ROUTE_PREFIX.'/country') }}" class="nav-link {{ Request::is('country') ? 'active' : '' }}"><i class="nav-icon fa fa-map-marker"></i><p>Country</p></a>
                      </li>
                      <li class="nav-item">
                          <a href="{{ url(ROUTE_PREFIX.'/states') }}" class="nav-link {{ Request::is('states') ? 'active' : '' }}"><i class="nav-icon fa fa-map-marker"></i><p>States</p></a>
                      </li>
                      <li class="nav-item">
                          <a href="{{ url(ROUTE_PREFIX.'/districts') }}" class="nav-link {{ Request::is('districts') ? 'active' : '' }}"><i class="nav-icon fa fa-map-marker"></i><p>Districts</p></a>
                      </li>
                  </ul>                  
              </li>
            @endcan           

            @can('role-list')
              <li class="nav-item {{ Request::is(ROUTE_PREFIX.'/roles*') ? 'menu-open' : '' }}">
                  <a href="#" class="nav-link {{ Request::is(ROUTE_PREFIX.'/roles*') ? 'active' : '' }}"><i class="nav-icon fas fa-certificate"></i><p>Manage Roles <i class="fas fa-angle-left right"></i></p></a>
                  <ul class="nav nav-treeview">
                      @can('role-edit', 'role-create')
                      <li class="nav-item">
                          <a href="{{ url(ROUTE_PREFIX.'/roles/create') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'/roles/create*') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Add New </p></a>
                      </li>
                      @endcan
                      <li class="nav-item">
                          <a href="{{ url(ROUTE_PREFIX.'/roles') }}" class="nav-link {{ Request::is(ROUTE_PREFIX.'/roles') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>List All</p></a>
                      </li>
                  </ul>
              </li>
            @endcan
            
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>