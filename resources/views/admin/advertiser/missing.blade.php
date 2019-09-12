@extends('layouts.admin.base')

@section('title', 'Advertisers Missing')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

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
                                            <input type="checkbox" name="filter-duplicate">
                                            <strong>Show duplicate advertisers</strong>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="filter-manager-account">
                                        <option value="0">All</option>
                                        @foreach($dataManagerAccount as $iter)
                                            <option value="{{ $iter->id }}">{{ $iter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-5 col-md-offset-1">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="filter-status">
                                        <option value="0">All</option>
                                        @foreach($dataStatus as $key => $iter)
                                            <option value="{{ $key }}">{{ $iter }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="advertiser-missing-table">
                        <thead>
                        <tr>
                            <th>EF ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Sales Manager</th>
                            <th>Account Manager</th>
                            <th>Action</th>
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

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('script')
<script>
    $(function() {

        var dataStatus = {!! json_encode($dataStatus) !!};

        var filterManager = $("select[name=filter-manager]").val();
        var filterManagerAccount = $("select[name=filter-manager-account]").val();
        var filterStatus = $("select[name=filter-status]").val();
        var filterDuplicate = $("input[name=filter-duplicate]").is(":checked") ? 1 : 0;

        var tableAdvertiserMissing = jDT('#advertiser-missing-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.advertiser.ajax.get.missing') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    manager: function(){ return filterManager; },
                    manager_account: function(){ return filterManagerAccount; },
                    status: function(){ return filterStatus; },
                    duplicate: function(){ return filterDuplicate; },
                    _token: "{{ csrf_token() }}",
                }
            },
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[1, 'asc']],
            columns: [
                {
                    title : 'EF ID',
                    data : 'ef_id',
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
                    title : 'Contact',
                    data : 'contact',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Email',
                    data : 'email',
                    render: function (data, type, full, meta) {

                        var html = "";

                        html = data.replace(/;/g, "<br />");
                        html = html.replace(/,/g, "<br />");

                        return html;
                    },
                },
                {
                    title : 'Sales Manager',
                    data : 'manager',
                    searchable: false,
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Account Manager',
                    data : 'manager_account',
                    searchable: false,
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Status',
                    data : 'status',
                    searchable: false,
                    render: function (data, type, full, meta) {

                        if(typeof dataStatus[data] !== "undefined"){
                            return dataStatus[data];
                        } else {
                            return "";
                        };
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

                        switch(full.status){
                            case 1 :
                                html += '<a type="button" target="_blank" style="margin-right: 5px" class="btn btn-sm btn-info" href="/admin/advertiser/view-missing/'+ full.id +'">View</a>';
                                html += '<a type="button" style="margin-right: 5px" class="btn btn-sm btn-primary" href="/admin/advertiser/add-missing/'+ full.id +'">Add</a>';
                                html += '<a type="button" class="btn btn-sm btn-danger" href="/admin/advertiser/ignore-missing/'+ full.id +'" onClick="return confirm(\'Ignore ?\')">Ignore</a>';
                                break;
                            case 2 :
                                html += '<a type="button" target="_blank" style="margin-right: 5px" class="btn btn-sm btn-info" href="/admin/advertiser/view-missing/'+ full.id +'">View</a>';
                                html += '<a type="button" class="btn btn-sm btn-primary" href="/admin/advertiser/add-missing/'+ full.id +'">Add</a>';
                                break;
                            case 3 :
                                html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="/admin/advertiser/view-missing/'+ full.id +'">View</a>';
                                break;
                            default :
                                break;
                        }

                        return html;
                    },
                },
            ]
        });


        $("#block-filter").on("change", "select[name=filter-manager]", function() {

            filterManager = $("select[name=filter-manager]").val();
            tableAdvertiserMissing.ajax.reload();
        });
        $("#block-filter").on("change", "select[name=filter-manager-account]", function() {

            filterManagerAccount = $("select[name=filter-manager-account]").val();
            tableAdvertiserMissing.ajax.reload();
        });
        $("#block-filter").on("change", "select[name=filter-status]", function() {

            filterStatus = $("select[name=filter-status]").val();
            tableAdvertiserMissing.ajax.reload();
        });
        $("#block-filter").on("change", "input[name=filter-duplicate]", function() {

            filterDuplicate = $("input[name=filter-duplicate]").is(":checked") ? 1 : 0;
            tableAdvertiserMissing.ajax.reload();
        });
    });
</script>
@endpush