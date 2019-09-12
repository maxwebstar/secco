@extends('layouts.admin.base')

@section('content')

    <div class="row">
        <div class="col-md-6">

            <div class="box dashboard-box-orange-light">
                <div class="box-header">
                    <h3 class="box-title">Activity</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-6">
                        <a href="{{ route('admin.io.index') }}" class="info-box bg-orange dashboard-button">
                            <span class="info-box-icon">{{ \DB::table('io')->where('status', 1)->count() }}</span>
                            <div class="info-box-content">
                                <span class="info-box-text">New I/O</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.offer.index') }}" class="info-box bg-yellow dashboard-button">
                            <span class="info-box-icon">{{ \DB::table('offer')->where('status', 1)->count() }}</span>
                            <div class="info-box-content">
                                <span class="info-box-text">New Offers</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.request.creative.index') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon">{{ \DB::table('request_creative')->where('status', 1)->count() }}</span>
                            <div class="info-box-content">
                                <span class="info-box-text">Creative Request</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.request.price.index') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon">{{ \DB::table('request_price')->where('status', 1)->count() }}</span>
                            <div class="info-box-content">
                                <span class="info-box-text">Price Changes</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('admin.request.mass.adjustment.index') }}" class="info-box bg-green dashboard-button">
                            <span class="info-box-icon">{{ \DB::table('request_mass_adjustment')->where('status', 1)->count() }}</span>
                            <div class="info-box-content">
                                <span class="info-box-text">Adjustments</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.request.status.index') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon">{{ \DB::table('request_status')->where('status', 1)->count() }}</span>
                            <div class="info-box-content">
                                <span class="info-box-text">Status Change</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.request.cap.index') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon">{{ \DB::table('request_cap')->where('status', 1)->count() }}</span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cap Changes</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.qb.customer.index') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon">{{ \DB::table('qb_customer')->where('status', 1)->count() }}</span>
                            <div class="info-box-content">
                                <span class="info-box-text">QB Customers</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-6">

            <div class="box dashboard-box-orange-light">
                <div class="box-header">
                    <h3 class="box-title">Request</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-6">
                        <a href="{{ route('admin.io.add') }}" class="info-box bg-orange dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">New Insertion Order</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.offer.add') }}" class="info-box bg-yellow dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">New Offer</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.request.mass.adjustment.add') }}" class="info-box bg-green dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-cog"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Adjustment Request</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.slick.puller.index') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-clipboard"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Slick Puller</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('admin.request.creative.add') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-paint-brush"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Creative Request</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.request.price.add') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Price Change</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.request.status.add') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-spinner"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Status Change</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.request.cap.add') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-umbrella"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cap Change</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-6">

            <div class="box dashboard-box-grey-light">
                <div class="box-header">
                    <h3 class="box-title">Accounting</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-6">
                        <a href="{{ route('admin.request.statistic.index') }}" class="info-box bg-orange dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-exchange"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Advertiser Stat Request</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.credit.cap.index') }}" class="info-box bg-yellow dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-credit-card"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Credit Cap</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('admin.fxrate.index') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-eur"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">FX Rate</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.prepay.index') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-credit-card"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pre-Pay Report</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="#" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Quick Books</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-3">

            <div class="box dashboard-box-grey-light">
                <div class="box-header">
                    <h3 class="box-title">Accounts</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <a href="{{ route('admin.advertiser') }}" class="info-box bg-orange dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-address-card"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Advertisers</span>
                                <span class="info-box-number"></span>
                                {{--<div class="progress">--}}
                                    {{--<div class="progress-bar" style="width: 70%"></div>--}}
                                {{--</div>--}}
                                {{--<span class="progress-description">--}}
                                    {{--70% Increase in 30 Days--}}
                                {{--</span>--}}
                            </div>
                        </a>
                        <a href="{{ route('admin.offer.profile') }}" class="info-box bg-yellow dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-envelope-open"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Offers</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="#" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-address-card-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Affiliates</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-3">

            <div class="box dashboard-box-grey-light">
                <div class="box-header">
                    <h3 class="box-title">Administration</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <a href="{{ route('admin.user') }}" class="info-box bg-orange dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Users</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="#" class="info-box bg-yellow dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-th"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Profiles</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                        <a href="{{ route('admin.permission.manage') }}" class="info-box bg-blue dashboard-button">
                            <span class="info-box-icon"><i class="fa fa-lock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Permissions</span>
                                <span class="info-box-number"></span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection