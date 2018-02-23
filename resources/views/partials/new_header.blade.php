<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="col-6">
                    <nav class="navbar navbar-default">
                        <div class="navbar-header">
                            <button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn"> <i class="fa fa-navicon"></i> </button>
                        </div>
                    </nav>
                </div>
                <div class="col-6">
                    <div class="content">
                        <ul>
                            <li><a href="#"></a></li>
                            <li class="profile"><a href="#"><span>{{ Auth::user()->username }}</span><span class="image"><img src="{{ asset('new_assets/images/profile-pic.png') }}"><span class="green-online"></span></span> <span><i class="fa fa-sort-desc"></i></span></a></li>
                            <li><a href="#"> <img src="{{ asset('new_assets/images/message-icon.png') }}"><span class="online2"></span> </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>