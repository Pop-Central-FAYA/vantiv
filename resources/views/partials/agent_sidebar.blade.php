<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('agency_asset/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Auth::user()->first_name. ' '.Auth::user()->last_name }}</p>
            </div>
        </div>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">
                    <li><a href="{{ route('dashboard') }}"><i class="fa fa-circle-o"></i> Agency Dashboard</a></li>
                    <li><a href="{{ route('agency.dashboard') }}"><i class="fa fa-circle-o"></i> Clients Dashboard</a></li>
                    {{--
                                        <li><a href="client-portfolio.html"><i class="fa fa-circle-o"></i> Clients Portfolio</a></li>
                    --}}
                </ul>
            </li>

            <li class="active treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Clients</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">
                    <li><a href="{{ route('clients.create') }}"><i class="fa fa-circle-o"></i> Add Client</a></li>
                    <li><a href="{{ route('clients.list') }}"><i class="fa fa-circle-o"></i> Clients List</a></li>
{{--
                    <li><a href="client-portfolio.html"><i class="fa fa-circle-o"></i> Clients Portfolio</a></li>
--}}
                </ul>
            </li>

            <li class=" treeview">
                <a href="#">
                    <i class="fa fa-th"></i>
                    <span>Brands Management</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('agency.brand.create') }}"><i class="fa fa-circle-o"></i> Create Brands</a></li>
                    <li><a href="{{ route('agency.brand.all') }}"><i class="fa fa-circle-o"></i> Brands List</a></li>

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
                    <li><a href="{{ route('agency.campaign.create') }}"><i class="fa fa-circle-o"></i> Create Campaign</a></li>
                    <li><a href="{{ route('agency.campaign.all') }}"><i class="fa fa-circle-o"></i> Campaign List</a></li>

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
                    <li><a href="{{ route('invoices.all') }}"><i class="fa fa-circle-o"></i> All Invoices</a></li>
                    <li><a href="{{ route('invoices.pending') }}"><i class="fa fa-circle-o"></i> Pending Invoices </a></li>

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
                    <li><a href="{{ route('agency_wallet.create') }}"><i class="fa fa-circle-o"></i> Credit Wallet</a></li>
                    <li><a href="{{ route('agency_wallet.statement') }}"><i class="fa fa-circle-o"></i> Wallet Statement </a></li>

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