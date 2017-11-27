<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Auth::user()->username }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active treeview">
                <a href="{{ asset('dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            @role('Broadcaster')
            <li class="active treeview">
                <a href="{{ asset('dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>Campaigns</span>
                </a>
            </li>
            <li class=" treeview">
                <a href="#">
                    <i class="fa fa-laptop"></i>
                    <span>MPOs</span>
                    <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('all-mpos') }}"><i class="fa fa-circle-o"></i> All MPOs</a></li>
                    <li><a href="{{ route('pending-mpos') }}"><i class="fa fa-circle-o"></i> Pending MPOs </a></li>
                </ul>
            </li>
            <li class="active treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Walkins</span>
                    <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('walkins.create') }}"><i class="fa fa-circle-o"></i> Add Walkins</a></li>
                    <li><a href="{{ route('walkins.all') }}"><i class="fa fa-circle-o"></i> Walkins List</a></li>

                </ul>

            </li>
            <li class="active treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Ads Management</span>
                    <span class="pull-right-container">
                                  <i class="fa fa-angle-left pull-right"></i>
                                </span>
                </a>

                <ul class="treeview-menu">
                    <li><a href="{{ route('adslot.all') }}"><i class="fa fa-circle-o"></i> Rate Card List</a></li>
                    <li><a href="{{ route('adslot.create') }}"><i class="fa fa-circle-o"></i> Add Rate Card</a></li>
                    <li><a href="{{ url('discounts') }}"><i class="fa fa-circle-o"></i> Discounts</a></li>

                </ul>

            </li>
            <li class="active treeview">
                <a href="{{ url('reports') }}">
                    <i class="fa fa-dashboard"></i> <span>Reports</span>
                </a>
            </li>
            @endrole

            @role('Admin')
            <li class="treeview">
                <a href="{{ route('user.list') }}">
                    <i class="fa fa-th"></i> <span>Users</span>
                </a>

            </li>
            <li class="treeview">
                <a href="{{ route('activity.index') }}">
                    <i class="fa fa-laptop"></i> <span>Activity Log</span>
                </a>
            </li>
            <li class="treeview">
                <a href="{{ route('role.index') }}">
                    <i class="fa fa-edit"></i> <span>Roles and Permission</span>

                </a>

            </li>
            <li class="treeview">
                <a href="{{ route('settings.general') }}">
                    <i class="fa fa-table"></i> <span>Settings</span>
                </a>
            </li>
            @endrole
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>