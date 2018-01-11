<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('agency_asset/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
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
                <a href="">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>

                <ul class="treeview-menu">
                    <li><a href="index.html"><i class="fa fa-circle-o"></i> Add Company</a></li>
                    <li><a href="company-search.html"><i class="fa fa-circle-o"></i> Company List</a></li>
                    <li><a href="client-portfolio.html"><i class="fa fa-circle-o"></i> Client Portfolio</a></li>

                </ul>

            </li>


            <li class=" treeview">
                <a href="#">
                    <i class="fa fa-th"></i>
                    <span>Campaign</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href=""><i class="fa fa-circle-o"></i> Create Campaign</a></li>
                    <li><a href="{{ route('agency.campaign.all') }}"><i class="fa fa-circle-o"></i> Campaign List</a></li>

                </ul>
            </li>

            <li class=" treeview">
                <a href="#">
                    <i class="fa fa-th"></i>
                    <span>Clients</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('all.clients') }}"><i class="fa fa-circle-o"></i> All Clients</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> Add New Client</a></li>

                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-file-archive-o"></i>
                    <span>Invoice</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="#"><i class="fa fa-circle-o"></i> All MPOs</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> Pending MPOs </a></li>

                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-google-wallet"></i> <span>Wallet</span>

                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="credit-wallet.html"><i class="fa fa-circle-o"></i> Credit Wallet</a></li>
                    <li><a href="walletstatement.html"><i class="fa fa-circle-o"></i> Wallet Statement </a></li>

                </ul>

            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-table"></i> <span>Report</span>

                </a>

            </li>



        </ul>
    </section>
    <!-- /.sidebar -->
</aside>