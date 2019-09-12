


<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        {{--<div class="user-panel">--}}
            {{--<div class="pull-left image">--}}
                {{--<img src="../../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">--}}
            {{--</div>--}}
            {{--<div class="pull-left info">--}}
                {{--<p>Alexander Pierce</p>--}}
                {{--<a href="#"><i class="fa fa-circle text-success"></i> Online</a>--}}
            {{--</div>--}}
        {{--</div>--}}
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
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>

            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-arrows"></i>
                    <span>Activity</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('admin.io.index') }}"><span class="label label-primary pull-right">{{ \DB::table('io')->where('status', 1)->count() }}</span> New I/O</a></li>
                    <li><a href="{{ route('admin.offer.index') }}"><span class="label label-primary pull-right">{{ \DB::table('offer')->where('status', 1)->count() }}</span> New Offers</a></li>
                    <li><a href="{{ route('admin.request.creative.index') }}"><span class="label label-primary pull-right">{{ \DB::table('request_creative')->where('status', 1)->count() }}</span> Creative Request</a></li>
                    <li><a href="{{ route('admin.request.price.index') }}"><span class="label label-primary pull-right">{{ \DB::table('request_price')->where('status', 1)->count() }}</span> Price Changes</a></li>
                    <li><a href="{{ route('admin.request.mass.adjustment.index') }}"><span class="label label-primary pull-right">0</span> Adjustments</a></li>
                    <li><a href="{{ route('admin.request.status.index') }}"><span class="label label-primary pull-right">{{ \DB::table('request_status')->where('status', 1)->count() }}</span> Status Change</a></li>
                    <li><a href="{{ route('admin.request.cap.index') }}"><span class="label label-primary pull-right">{{ \DB::table('request_cap')->where('status', 1)->count() }}</span> Cap Changes</a></li>
                    <li><a href="{{ route('admin.pipedrive.index') }}"><span class="label label-primary pull-right">{{ \DB::table('pipe_drive_deal')->where('status', 1)->count() }}</span> PipeDrive Deals</a></li>
                    <li><a href="{{ route('admin.qb.customer.index') }}"><span class="label label-primary pull-right">{{ \DB::table('qb_customer')->where('status', 1)->count() }}</span> QB Customers</a></li>
                    <li><a href="{{ route('admin.advertiser.missing') }}"><span class="label label-primary pull-right">{{ \DB::table('advertiser_missing')->where('status', 1)->count() }}</span> Advertisers Missing</a></li>
                    <li><a href="{{ route('admin.request.creative.missing') }}"><span class="label label-primary pull-right">{{ \DB::table('offer_creative_missing')->where('status', 1)->count() }}</span> Creative Missing</a></li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-pencil"></i>
                    <span>Requests</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('admin.io.add') }}"><i class="fa fa-file-text-o"></i> New Insertion Order</a></li>
                    <li><a href="{{ route('admin.offer.add') }}"><i class="fa fa-file-text-o"></i> New Offer</a></li>
                    <li><a href="{{ route('admin.request.mass.adjustment.add') }}"><i class="fa fa-cog"></i> Adjustment Request</a></li>
                    <li><a href="{{ route('admin.slick.puller.index') }}"><i class="fa fa-clipboard"></i> Slick Puller</a></li>
                    <li><a href="{{ route('admin.request.creative.add') }}"><i class="fa fa-paint-brush"></i> Creative Request</a></li>
                    <li><a href="{{ route('admin.request.price.add') }}"><i class="fa fa-money"></i> Price Change</a></li>
                    <li><a href="{{ route('admin.request.status.add') }}"><i class="fa fa-spinner"></i> Status Change</a></li>
                    <li><a href="{{ route('admin.request.cap.add') }}"><i class="fa fa-umbrella"></i> Cap Change</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-university"></i>
                    <span>Accounting</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('admin.request.statistic.index') }}"><i class="fa fa-exchange"></i> Advertiser Stat Request</a></li>
                    <li><a href="{{ route('admin.credit.cap.index') }}"><i class="fa fa-credit-card"></i> Credit Cap</a></li>
                    <li><a href="{{ route('admin.fxrate.index') }}"><i class="fa fa-eur"></i> FX Rate</a></li>
                    <li><a href="{{ route('admin.prepay.index') }}"><i class="fa fa-credit-card"></i> Pre-pay Report</a></li>
                    <li><a href=""><i class="fa fa-money"></i> Quick Books</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-map"></i>
                    <span>Accounts</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('admin.advertiser') }}"><i class="fa fa-address-card"></i> Advertisers</a></li>
                    <li><a href="{{ route('admin.offer.profile') }}"><i class="fa fa-envelope-open"></i> Offers</a></li>
                    <li><a href=""><i class="fa fa-address-card-o"></i> Affiliates</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-table"></i> <span>Administration</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('admin.user') }}"><i class="fa fa-users"></i> Users</a></li>
                    <li><a href=""><i class="fa fa-th"></i> Profiles</a></li>
                    <li><a href="{{ route('admin.permission.manage') }}"><i class="fa fa-lock"></i> Permissions</a></li>
                    <li><a href="{{ route('admin.domain') }}"><i class="fa fa-list-ul"></i> Domains list</a></li>
                    <li><a href="{{ route('admin.email.template') }}"><i class="fa fa-envelope-o"></i> Emails Templates</a></li>
                    <li><a href="{{ route('admin.term.template') }}"><i class="fa fa-handshake-o"></i> Terms Templates</a></li>
                    <li><a href="{{ route('admin.access.index') }}"><i class="fa fa-lock"></i> Access</a></li>
                    @if(Auth::user()->hasRole('admin'))
                        <li><a href="{{ route('auth.qb.login') }}"><i class="fa fa-refresh"></i> Refresh QB Access</a></li>
                    @endif
                </ul>
            </li>



            <li><a href="https://adminlte.io/docs"><i class="fa fa-book"></i> <span>Documentation</span></a></li>
            <li class="header">LABELS</li>
            <li><a href="#"><i class="fa fa-circle-o text-red"></i> <span>Important</span></a></li>
            <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Warning</span></a></li>
            <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Information</span></a></li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>