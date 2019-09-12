@extends('layouts.admin.base')

@section('title', 'Advertisers')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-11">

            <div class="box box-default">
                <div class="box-header">
                    <a class="btn btn-primary" href="{{ route('admin.advertiser.add') }}" role="button">Create new Advertiser</a>
                </div>
                <div class="box-body">

                    <div id="block-filter">
                        <div class="row">
                            <div class="col-md-5">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="filter-manager">
                                        @if($auth->hasRole('admin'))
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
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="filter-duplicate-name">
                                            <strong>Show advertisers with duplicate name</strong>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="advertiser-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>lt_id</th>
                            <th>ef_id</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Sales Manager</th>
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

        var filterManager = $("select[name=filter-manager]").val();
        var filterDuplicateName = $("input[name=filter-duplicate-name]").is(":checked") ? 1 : 0;

        var tableAdvertiser = jDT('#advertiser-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.advertiser.ajax.get') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    manager: function(){ return filterManager; },
                    duplicate_name: function(){ return filterDuplicateName; },
                    _token: "{{ csrf_token() }}",
                }
            },
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[7, 'desc']],
            columns: [
                {
                    title : 'ID',
                    data : 'id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'lt_id',
                    data : 'lt_id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'ef_id',
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
                    width : '25%',
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

                        html += '<a type="button" target="_blank" style="margin-right: 5px" class="btn btn-sm btn-info" href="/admin/advertiser/edit/'+ full.id +'">Edit</a>';
                        html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="/admin/advertiser/profile/'+ full.id +'">Profile</a>';

                        return html;
                    },
                },
            ]
        });


        $("#block-filter").on("change", "select[name=filter-manager]", function() {

            filterManager = $("select[name=filter-manager]").val();
            tableAdvertiser.ajax.reload();

        });
        $("#block-filter").on("change", "input[name=filter-duplicate-name]", function() {

            filterDuplicateName = $("input[name=filter-duplicate-name]").is(":checked") ? 1 : 0;
            tableAdvertiser.ajax.reload();

            if(filterDuplicateName){
                tableAdvertiser.order([3, 'asc']).draw();
            } else {
                tableAdvertiser.order([6, 'asc']).draw();
            }
        });
    });
</script>
@endpush