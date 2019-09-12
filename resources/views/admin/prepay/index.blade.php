@extends('layouts.admin.base')

@section('title', 'Pre Pay')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-11">

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
                                        @if($auth->hasRole(['sales']))
                                            <option value="{{ $auth->id }}">{{ $auth->name }}</option>
                                        @else
                                            <option value="0">All</option>
                                            @foreach($dataManager as $iter)
                                                <option value="{{ $iter->id }}">{{ $iter->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-5 col-md-offset-2">
                                <a class="btn btn-info pull-right" style="margin-top: 20px;" href="{{ route('admin.prepay.csv.export') }}" role="button ">Export CSV</a>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="pre-pay-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>LT ID</th>
                            <th>EF ID</th>
                            <th>Advertiser Name</th>
                            <th>Payments</th>
                            <th>Revenue</th>
                            <th>MTD Revenue</th>
                            <th>Prepay Remaining</th>
                            <th>Used(%)</th>
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

@push('css')
<style type="text/css">
    .table-row-td-yellow .row-td-color{ color: #ffb244; font-weight: 600; }
    .table-row-td-green .row-td-color{ color: #2dcb73; font-weight: 600; }
    .table-row-td-red .row-td-color{ color: red; font-weight: 600; }
</style>
@endpush

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('fancybox')
<script src="{{ asset('js/admin/fancybox.js') }}"></script>
@endpush

@push('script')
<script>

    var tablePrePay = new Object();

    $(function() {

        var filterManager = $("select[name=filter-manager]").val();

        tablePrePay = jDT('#pre-pay-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.prepay.ajax.get') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    created_by: function(){ return filterManager; },
                    _token: "{{ csrf_token() }}",
                }
            },
            createdRow: function(row, full, dataIndex){

                if(full.used_percent >= 95){
                    $(row).addClass('table-row-td-red');
                } else if(full.used_percent >= 50){
                    $(row).addClass('table-row-td-yellow');
                }
            },
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[3, 'asc']],
            columns: [
                {
                    title : 'ID',
                    data : 'id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'LT ID',
                    data : 'lt_id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'EF ID',
                    data : 'ef_id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Advertiser Name',
                    data : 'name',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Payments',
                    data : 'amount',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Revenue',
                    data : 'revenue',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'MTD Revenue',
                    data : 'revenue_mtd',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Prepay Remaining',
                    data : 'balance_remaining',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Used (%)',
                    data : 'used_percent',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        return data + "%";
                    },
                }
            ]
        });


        $("#block-filter").on("change", "select[name=filter-manager]", function() {

            filterManager = $("select[name=filter-manager]").val();
            tablePrePay.ajax.reload();
        });

    });

</script>
@endpush