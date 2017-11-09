<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('asset/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>
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


            <li class="treeview">
                <a href="{{ route('user.list') }}">
                    <i class="fa fa-th"></i>
                    <span>Users</span>

                </a>

            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-laptop"></i>
                    <span>Activity Log</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-edit"></i> <span>Roles and Permission</span>

                </a>

            </li>
            <li class="treeview">
                <a href="{{ route('settings.general') }}">
                    <i class="fa fa-table"></i> <span>Settings</span>

                </a>

            </li>



        </ul>
    </section>
    <!-- /.sidebar -->
</aside>