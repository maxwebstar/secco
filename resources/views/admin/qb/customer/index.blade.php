@extends('layouts.admin.base')

@section('title', 'QB Customer')

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
                                    <select class="form-control" name="filter-manager">
                                        <option value="0">All</option>
                                        @foreach($dataManager as $iter)
                                            <option value="{{ $iter->id }}">{{ $iter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-5 col-md-offset-1">

                                <div class="form-group">
                                    <label></label>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="filter-only-active">
                                            <strong>Show only active</strong>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="filter-status">
                                        <option value="0">All</option>
                                        @foreach($data->arrStatus as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="qb-customer-table">
                        <thead>
                        <tr>
                            <th>QB ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Company</th>
                            <th>Advertiser</th>
                            <th>Active</th>
                            <th>Status</th>
                            <th>QB Created</th>
                            <th>Actions</th>
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

    var tableQBCustomer = new Object();

    $(function() {

        var dataStatus = {!! json_encode($data->arrStatus) !!};

        var filterManager = $("select[name=filter-manager]").val();
        var filterStatus = $("select[name=filter-status]").val();
        var filterActive = $("input[name=filter-only-active]").is(":checked") ? 1 : 0;;

        tableQBCustomer = jDT('#qb-customer-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.qb.customer.ajax.get') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    manager: function(){ return filterManager; },
                    status: function(){ return filterStatus; },
                    active: function(){ return filterActive; },
                    _token: "{{ csrf_token() }}",
                }
            },
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[8, 'desc']],
            columns: [
                {
                    title : 'QB ID',
                    data : 'quickbook_id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Name',
                    data : 'name',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Email',
                    data : 'email',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Phone',
                    data : 'phone',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Company',
                    data : 'company',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Advertiser',
                    data : 'advertiser_name',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Active',
                    data : 'active',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {

                        if(data){
                            return '<i class="fa fa-check-circle fa-lg icon-green"></i>';
                        } else {
                            return '<i class="fa fa-minus-circle fa-lg icon-red"></i>';
                        };
                    },
                },
                {
                    title : 'Status',
                    data : 'status',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {

                        if(typeof dataStatus[data] !== "undefined"){
                            return dataStatus[data];
                        } else {
                            return "";
                        };
                    },
                },
                {
                    title : 'QB Created',
                    data : 'created_qb',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Action',
                    searchable: false,
                    orderable: false,
                    data : 'id',
                    render: function (data, type, full, meta) {

                        var html = '';
                        html += '<a type="button" target="_blank" class="btn btn-sm btn-primary" href="/admin/qb/customer/view/'+full.id+'">View</a>&nbsp;';

                        switch(full.status){
                            case 1 : /*not attached*/
                                html += '<a type="button" class="btn btn-sm btn-success" onClick="attacheAdvertiser('+full.id+')">Attach</a>&nbsp;';
                                break;
                            case 2 : /*attached to advertiser*/
                                html += '<a type="button" class="btn btn-sm btn-success" onClick="attacheAdvertiser('+full.id+')">Edit Attached</a>&nbsp;';
                                break;
                        }

                        return html;
                    },
                },
            ]
        });


        $("#block-filter").on("change", "select[name=filter-status]", function() {

            filterStatus = $("select[name=filter-status]").val();
            tableQBCustomer.ajax.reload();
        });
        $("#block-filter").on("change", "select[name=filter-manager]", function() {

            filterManager = $("select[name=filter-manager]").val();
            tableQBCustomer.ajax.reload();
        });
        $("#block-filter").on("change", "input[name=filter-only-active]", function() {

            filterActive = $("input[name=filter-only-active]").is(":checked") ? 1 : 0;
            tableQBCustomer.ajax.reload();
        });

    });

    function attacheAdvertiser(id)
    {
        $.fancybox.open({
            src: '/admin/qb/customer/attache/'+id,
            type : 'iframe',
            opts : {
                iframe : {
                    css : {
                        width: '70%',
                        height: '70%',
                    },
                    attr : {
                        scrolling : 'yes'
                    }
                },
                afterClose : function() {

                    tableQBCustomer.ajax.reload();
                }
            }
        });
    }

</script>
@endpush