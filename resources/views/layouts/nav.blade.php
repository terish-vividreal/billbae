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
            <li class="nav-item">
                <a href="{{ url(ROUTE_PREFIX.'/home') }}" class="nav-link {{ Request::is('home*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            @php   $role = (Auth::user()->is_admin == 1) ? 'admin/' : '' ; @endphp

            @if(Auth::user()->is_admin == 1)
                    <li class="nav-item {{ Request::is('admin/stores*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('admin/stores*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i><p>Manage Stores <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">                    
                            
                            @can('user-create')
                            <li class="nav-item">
                                <a href="{{ url(ROUTE_PREFIX.'/stores/create') }}" class="nav-link {{ Request::is('admin/stores/create*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Add New </p>
                                </a>
                            </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ url(ROUTE_PREFIX.'/stores') }}" class="nav-link {{ Request::is('admin/stores') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>List All</p>
                                </a>
                            </li>
                        </ul>
                    </li>

            @else
                  @can('user-list')         
                    <li class="nav-item {{ Request::is('users*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('users*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i><p>Manage Users <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">                    
                            
                            @can('user-create')
                            <li class="nav-item">
                                <a href="{{ url(ROUTE_PREFIX.'/users/create') }}" class="nav-link {{ Request::is('users/create*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Add New </p>
                                </a>
                            </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ url(ROUTE_PREFIX.'/users') }}" class="nav-link {{ Request::is('users') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>List All</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                  @endcan

            @endif

                    
            @can('role-list')
            <li class="nav-item {{ Request::is('roles*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('roles*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-key"></i><p>Manage Roles <i class="fas fa-angle-left right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @can('role-edit', 'role-create')
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/roles/create') }}" class="nav-link {{ Request::is('roles/create*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Add New </p>
                        </a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/roles') }}" class="nav-link {{ Request::is('roles') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>List All</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            @can('manage-business-types')
            <li class="nav-item {{ Request::is('business-types*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('business-types*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-key"></i><p>Manage Business Types <i class="fas fa-angle-left right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url($role.'business-types') }}" class="nav-link {{ Request::is('business-types') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>List All</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            @can('service-category-list')
            <li class="nav-item {{ Request::is('service-category*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('service-category*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-key"></i><p>Service category <i class="fas fa-angle-left right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @can('service-category-edit', 'service-category-create')
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/service-category/create') }}" class="nav-link {{ Request::is('service-category/create*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Add New </p>
                        </a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a href="{{ url(ROUTE_PREFIX.'/service-category') }}" class="nav-link {{ Request::is('service-category') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>List All</p>
                        </a>
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