@extends('layouts.admin.base')

@section('title', 'PipeDrive')

@section('content')

    <div class="row">
        <div class="col-md-11">

            <div class="box box-default">
                <div class="box-header">

                </div>
                <div class="box-body">

                    <div id="block-filter">
                        <div class="row">
                            <div class="col-md-5">

                                <div class="form-group">
                                    <label></label>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="filter-wuthout-advertiser">
                                            <strong>Show deals without advertisers</strong>
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-1 col-md-offset-1">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="advertiser-network">
                                        @foreach($dataNetwork as $iter)
                                            <option value="{{ $iter->field_name }}" {{ $iter->checkSelected() ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-4">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="search-advertiser">
                                        <option></option>
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-5">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="filter-manager">
                                        @if($auth->hasRole('admin'))
                                            <option value="0">All</option>
                                            @foreach($dataManager as $iter)
                                                <option value="{{ $iter->pipedrive_id }}">{{ $iter->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="{{ $auth->pipedrive_id }}">{{ $auth->name }}</option>
                                        @endif
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-5 col-md-offset-1">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="filter-status">
                                        @foreach($dataStatus as $id => $name)
                                            <option value="{{ $id }}" {{ $name == "Pending" ? " selected" : "" }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="deal-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>DealID</th>
                            <th>Advertiser</th>
                            <th>Campaign</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Created At</th>
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

@push('select2')
<script src="{{ asset('js/admin/select2.js') }}"></script>
@endpush
@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush
@push('fancybox')
<script src="{{ asset('js/admin/fancybox.js') }}"></script>
@endpush

@push('script')
<script>
    $(function() {

        var dataStatus = {!! json_encode($dataStatus) !!};
        var dataCurrency = {!! json_encode($dataCurrency) !!};

        var filterWithoutAdvertiser = $("input[name=filter-wuthout-advertiser]").is(":checked") ? 1 : 0;
        var filterManager = $("select[name=filter-manager]").val();
        var filterStatus = $("select[name=filter-status]").val();

        var tableDeal = jDT('#deal-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.pipedrive.ajax.get') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    without_advertiser: function(){ return filterWithoutAdvertiser },
                    manager: function(){ return filterManager; },
                    status: function(){ return filterStatus; },
                    _token: "{{ csrf_token() }}",
                }
            },
            drawCallback: function( settings ) {

                jQuery('[data-toggle="tooltip"]').tooltip();

                jQuery(".btn-io-show-detail").fancybox({
                    maxWidth	: 800,
                    maxHeight	: 600,
                    fitToView	: false,
                    width	: '70%',
                    height	: '70%',
                    autoSize	: false,
                    closeClick	: false,
                    openEffect	: 'none',
                    closeEffect	: 'none'
                });
            },
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[6, 'desc']],
            columns: [
                {
                    title : 'ID',
                    data : 'id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'DealID',
                    data : 'pd_deal_id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Advertiser',
                    data : 'advertiser_name',
                    render: function (data, type, full, meta) {

                        var html = '';

                        if(data) {
                            if (full.advertiser_id) {
                                html = '<i class="fa fa-check-circle fa-lg icon-green" data-toggle="tooltip" title="Advertiser already exist"></i>';
                            } else {
                                html = '<i class="fa fa-minus-circle fa-lg icon-red" data-toggle="tooltip" title="Advertiser not exist"></i>';
                            }

                            html += ' ' + data;
                        }
                        return html;
                    },
                },
                {
                    title : 'Campaign',
                    data : 'io_campaign_name',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Manager',
                    data : 'manager',
                    render: function (data, type, full, meta) {

                        return data;
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
                    title : 'Created',
                    data : 'created_at',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Action',
                    data : '',
                    render: function (data, type, full, meta) {

                        if(typeof dataStatus[full.status] !== "undefined"){
                            var status = dataStatus[full.status];
                        } else {
                            var status = "";
                        }
                        if(typeof dataCurrency[full.currency_id] !== "undefined"){
                            var currency = dataCurrency[full.currency_id];
                        } else {
                            var currency = "";
                        }

                        var html = '<a type="button" class="btn btn-sm btn-info btn-io-show-detail" href="#io-show-detail-'+full.id+'">View</a>&nbsp';

                        if(full.status != 0) {
                            if (full.advertiser_id) {
                                var htmlBtnNew = '<a type="button" target="_blank" class="btn btn-sm btn-default" href="/admin/advertiser/add/' + full.id + '">New Advertiser</a>';
                                var htmlBtnNewFancy = '<a type="button" target="_blank" class="btn btn-sm btn-default pull-right" href="/admin/advertiser/add/' + full.id + '" style="margin-left: 5px;">New Advertiser</a>';
                                    htmlBtnNew += '<a type="button" target="_blank" class="btn btn-sm btn-primary" href="/admin/io/add/' + full.id + '" style="margin-left: 5px;">New IO</a>';
                                    htmlBtnNewFancy += '<a type="button" target="_blank" class="btn btn-sm btn-primary pull-right" href="/admin/io/add/' + full.id + '" style="margin-left: 5px;">New IO</a>';
                            } else {
                                var htmlBtnNew = '<a type="button" target="_blank" class="btn btn-sm btn-primary" href="/admin/advertiser/add/' + full.id + '">New Advertiser</a>';
                                var htmlBtnNewFancy = '<a type="button" target="_blank" class="btn btn-sm btn-primary pull-right" href="/admin/advertiser/add/' + full.id + '" style="margin-left: 5px;">New Advertiser</a>';
                                    htmlBtnNew += '<a type="button" target="_blank" class="btn btn-sm btn-default" href="/admin/io/add/' + full.id + '" style="margin-left: 5px;">New IO</a>';
                                    htmlBtnNewFancy += '<a type="button" target="_blank" class="btn btn-sm btn-default pull-right" href="/admin/io/add/' + full.id + '" style="margin-left: 5px;">New IO</a>';
                            }
                        } else {
                            var htmlBtnNew = '';
                            var htmlBtnNewFancy = '';
                        }

                        if(full.status == 1){
                            var htmlBtnDelete = '&nbsp<a type="button" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')" href="/admin/pipedrive/delete/'+full.id+'">Remove</a>';
                            var htmlBtnDeleteFancy = '<a type="button" class="btn btn-sm btn-danger pull-right" onclick="return confirm(\'Are you sure?\')" href="/admin/pipedrive/delete/'+full.id+'" style="margin-left: 5px;">Remove</a>';
                        } else {
                            var htmlBtnDelete = '';
                            var htmlBtnDeleteFancy = '';
                        }

                        html += htmlBtnNew + htmlBtnDelete;
                        html +=
                        '<div id="io-show-detail-'+full.id+'" class="" style="display:none; width: 70%;">' +
                            '<div class="box" style="margin-top: 20px;">' +
                            '<div class="box-header"><h3 class="box-title">'+full.io_campaign_name+'</h3></div>' +
                            '<div class="box-body no-padding">' +
                            '<table class="table table-striped">' +
                                '<thead>'+
                                    '<tr>' +
                                        '<th>Key</th><th>Value</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody>' +

                                        '<tr><td>DealID</td><td>'+full.pd_deal_id+'</td></tr>' +
                                        '<tr><td>OrganizationID</td><td>'+full.pd_organization_id+'</td></tr>' +
                                        '<tr><td>PersonID</td><td>'+full.pd_person_id+'</td></tr>' +
                                        '<tr><td>UserID</td><td>'+full.pd_user_id+'</td></tr>' +
                                        '<tr><td>Campaign Name</td><td>'+full.io_campaign_name+'</td></tr>' +
                                        '<tr><td>Currency</td><td>'+currency+'</td></tr>' +
                                        '<tr><td>Advertiser Name</td><td>'+full.advertiser_name+'</td></tr>' +
                                        '<tr><td>Advertiser Contact</td><td>'+full.advertiser_contact+'</td></tr>' +
                                        '<tr><td>Advertiser Country</td><td>'+full.advertiser_country+'</td></tr>' +
                                        "<tr><td>Advertiser Address</td><td>"+full.advertiser_street1+"</td></tr>" +
                                        '<tr><td>Advertiser Zip</td><td>'+full.advertiser_zip+'</td></tr>' +
                                        "<tr><td>Advertiser Email</td><td>"+full.advertiser_email+"</td></tr>" +
                                        '<tr><td>Advertiser Phone</td><td>'+full.advertiser_phone+'</td></tr>' +
                                        '<tr><td>Manager</td><td>'+full.manager+'</td></tr>' +
                                        '<tr><td>Status</td><td>'+status+'</td></tr>' +
                                        '<tr><td>Updated</td><td>'+full.updated_at+'</td></tr>' +
                                        '<tr><td>Created</td><td>'+full.created_at+'</td></tr>' +

                                '</tbody>' +
                            '</table>' +
                            '</div>' +
                            '<div class="box-footer">' +
                                '<button data-fancybox-close type="button" class="btn btn-sm btn-danger pull-right" style="margin-left: 5px;">Cancel</button>' +
                                htmlBtnDeleteFancy +
                                htmlBtnNewFancy +
                            '</div>' +
                            '</div>' +
                        '</div>';

                        return html;
                    },
                },
            ]

        });

        $("#block-filter").on("change", "select[name=filter-manager]", function() {

            filterManager = $("select[name=filter-manager]").val();
            tableDeal.ajax.reload();
        });
        $("#block-filter").on("change", "select[name=filter-status]", function() {

            filterStatus = $("select[name=filter-status]").val();
            tableDeal.ajax.reload();
        });
        $("#block-filter").on("change", "input[name=filter-wuthout-advertiser]", function() {

            filterWithoutAdvertiser = $("input[name=filter-wuthout-advertiser]").is(":checked") ? 1 : 0;
            tableDeal.ajax.reload();
        });

        $('select[name=search-advertiser]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Search Advertiser",
            ajax: {
                url: "{{ route('admin.ajax.search.advertiser') }}",
                method : 'post',
                data: function (params) {
                    var query = {
                        search: params.term,
                        network: $("select[name=advertiser-network]").val(),
                        _token : "{{ csrf_token() }}",
                    }

                    return query;
                },
                processResults: function (data, params) {

                    return {
                        results: data.results,
                    };
                }
            }
        });

    });
</script>
@endpush