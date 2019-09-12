@extends('layouts.admin.base')

@section('title', 'Mass Adjustment Request')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="box box-default">
                <div class="box-header">
                    {{--<a class="btn btn-primary" href="{{ route('admin.advertiser.add') }}" role="button">Create new Advertiser</a>--}}
                </div>
                <div class="box-body">

                    <div id="block-filter">
                        <div class="row">
                            <div class="col-md-5">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="filter-created-by">
                                        @if($auth->hasRole(['admin', 'ad_ops', 'accounting', 'account_manager']))
                                            <option value="0">All</option>
                                            @foreach($dataManager as $iter)
                                                <option value="{{ $iter->id }}">{{ $iter->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="{{ $auth->id }}">{{ $auth->name }}</option>
                                        @endif
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-5 col-md-offset-1">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="filter-status">
                                        <option value="0">All</option>
                                        @foreach($dataStatus as $id => $name)
                                            <option value="{{ $id }}" {{ $name == 'New' ? ' selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="mass-adjustment-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Network</th>
                            <th>Campaign Name</th>
                            <th>Affiliate</th>
                            <th>Date</th>
                            <th>Clicks</th>
                            <th>Qualified</th>
                            <th>Approved</th>
                            <th>Revenue</th>
                            <th>Commission</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('fancybox')
<script src="{{ asset('js/admin/fancybox.js') }}"></script>
@endpush

@push('script')
<script>

    var tableRequestMassAdjustment = new Object();

    $(function() {

        var dataStatus = {!! json_encode($dataStatus) !!};
        var dataType = {!! json_encode($dataType) !!};

        var filterCreatedBy = $("select[name=filter-created-by]").val();
        var filterStatus = $("select[name=filter-status]").val();

        tableRequestMassAdjustment = jDT('#mass-adjustment-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.request.mass.adjustment.ajax.get') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    created_by: function(){ return filterCreatedBy; },
                    status: function(){ return filterStatus; },
                    _token: "{{ csrf_token() }}",
                }
            },
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[9, 'desc']],
            columns: [
                {
                    title : 'ID',
                    data : 'id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Network',
                    data : 'short_name',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Campaign Name',
                    data : 'campaign_name',
                    render: function (data, type, full, meta) {

                        var id = "";

                        switch(full.network_id){
                            case 1 : id = full.offer_lt_id; break;
                            case 2 : id = full.offer_ef_id; break;
                            default : break
                        }

                        if(id){
                            var html = "(id: " + id + ") " + data;
                        } else {
                            var html = data;
                        }

                        if(!html){
                            switch(full.offer_id){
                                case "0000" :
                                    html = "(0000) Secco Test Offer";
                                    break;
                                default :
                                    break;
                            }
                        }

                        return html;
                    },
                },
                {
                    title : 'Affiliate',
                    data : 'affiliate_name',
                    orderable: false,
                    render: function (data, type, full, meta) {

                        var id = "";
                        var html = "";

                        switch(full.network_id){
                            case 1 : id = full.affiliate_lt_id; break;
                            case 2 : id = full.affiliate_ef_id; break;
                            default : break
                        }

                        if(id){
                            html = "(id: " + id + ") " + data;
                        } else {
                            html = data;
                        }

                        if(!html){
                            switch(full.affiliate_id){
                                case "000" :
                                    html = "(100) Secco House";
                                    break;
                                case "0000" :
                                    html = "(0000) Secco Test Affiliate";
                                    break;
                                default :
                                    break;
                            }
                        }

                        return html;
                    },
                },
                {
                    title : 'Date',
                    data : 'date',
                    orderable: false,
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Clicks',
                    data : 'click',
                    orderable: false,
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Qualified',
                    data : 'qualified',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Approved',
                    data : 'approved',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Revenue',
                    data : 'revenue',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Commission',
                    data : 'commission',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Type',
                    data : 'type',
                    render: function (data, type, full, meta) {

                        if(typeof dataType[data] !== "undefined"){
                            return dataType[data];
                        } else {
                            return "";
                        };
                    },
                },
                {
                    title : 'Status',
                    data : 'status',
                    render: function (data, type, full, meta) {

                        if(typeof dataStatus[data] !== "undefined"){
                            return dataStatus[data];
                        } else {
                            return "";
                        };
                    },
                },
                {
                    title : 'Created By',
                    data : 'created_name',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Created At',
                    data : 'created_at',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Action',
                    searchable: false,
                    orderable: false,
                    data : '',
                    render: function (data, type, full, meta) {

                        var html = '';

                        html += '<a type="button" target="_blank" class="btn btn-sm btn-primary" href="/admin/request/massadjustment/view/'+ full.id +'">View</a>&nbsp;';

                        switch(full.status){
                            case 1 : /*new*/
                                html += '<a type="button" class="btn btn-sm btn-success" href="/admin/request/massadjustment/approve/'+ full.id +'" onClick="return confirm(\'Approve ?\')">Approve</a>&nbsp;';
                                html += '<a type="button" class="btn btn-sm btn-danger" onClick="decline('+full.id+')">Decline</a>&nbsp;';
                                html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="/admin/request/massadjustment/edit/'+ full.id +'">Edit</a>&nbsp;';
                                break;
                            case 2 : /*declined*/
                                html += '<a type="button" class="btn btn-sm btn-success" href="/admin/request/massadjustment/approve/'+ full.id +'">Approve</a>&nbsp;';
                                html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="/admin/request/massadjustment/edit/'+ full.id +'">Edit</a>&nbsp;';
                                break;
                            case 3 : /*approved*/
                                break;

                        }

                        return html;
                    },
                },
            ]
        });


        $("#block-filter").on("change", "select[name=filter-status]", function() {

            filterStatus = $("select[name=filter-status]").val();
            tableRequestMassAdjustment.ajax.reload();
        });
        $("#block-filter").on("change", "select[name=filter-created-by]", function() {

            filterCreatedBy = $("select[name=filter-created-by]").val();
            tableRequestMassAdjustment.ajax.reload();
        });

    });


    function decline(id)
    {
        if(confirm('Decline ?')) {

            $.fancybox.open({
                src: '/admin/request/massadjustment/decline/'+id,
                type : 'iframe',
                opts : {
                    iframe : {
                        css : {
                            width: '70%'
                        },
                        attr : {
                            scrolling : 'yes'
                        }
                    },
                    afterClose : function() {

                        tableRequestMassAdjustment.ajax.reload();
                    }
                }
            });
        }
    }

</script>
@endpush