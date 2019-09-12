@extends('layouts.admin.base')

@section('title', 'Credit Cap')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-11">

            <div class="box box-default">
                <div class="box-header">
                    <a class="btn btn-info" href="{{ route('admin.credit.cap.report') }}" role="button">Advertiser Report</a>
                    <a class="btn btn-info" href="{{ route('admin.credit.cap.report.month') }}" role="button">Advertiser Report by Month</a>
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
                            <div class="col-md-5 col-md-offset-1">
                                <i class='fa fa-hourglass-half'></i>=n - it is not 6 months (where "n" is number of months);<br>
                                <i class='fa fa-hand-paper-o'></i> - set by manual;
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="credit-cap-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>LT ID</th>
                            <th>EF ID</th>
                            <th>Advertiser Name</th>
                            <th>AR</th>
                            <th>Revenue MTD</th>
                            <th>Balance</th>
                            <th>Cap</th>
                            <th>Cap (%)</th>
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

    var tableCreditCap = new Object();

    $(function() {

        var filterManager = $("select[name=filter-manager]").val();

        tableCreditCap = jDT('#credit-cap-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.credit.cap.ajax.get') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    manager: function(){ return filterManager; },
                    _token: "{{ csrf_token() }}",
                }
            },
            createdRow: function(row, full, dataIndex){

                switch(full.num_month){
                    case 4 :
                    case 5 :
                        $(row).addClass('table-row-td-yellow');
                        break;
                    case 6 :
                        $(row).addClass('table-row-td-green');
                        break;
                    default :
                        if(full.num_month < 4){
                            $(row).addClass('table-row-td-red');
                        }
                        break;

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
                    title : 'AR',
                    data : 'ar',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Revenue MTD',
                    data : 'revenue_mtd',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Balance',
                    data : 'balance',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Cap',
                    data : 'cap',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        var html = "";

                        if(!full.is_6_month){
                            var month6 = "&nbsp;<i class='fa fa-hourglass-half'></i>=" + full.num_month;
                        } else {
                            var month6 = "";
                        }

                        if(full.cap_type == 1){
                            var manual = "&nbsp;<i class='fa fa-hand-paper-o'></i>";
                        } else {
                            var manual = "";
                        }

                        html = data + month6 + manual;

                        return html;
                    },
                },
                {
                    title : 'Cap (%)',
                    data : 'cap_percent',
                    className : 'row-td-color',
                    render: function (data, type, full, meta) {

                        return data + "%";
                    },
                }
            ]
        });


        $("#block-filter").on("change", "select[name=filter-manager]", function() {

            filterManager = $("select[name=filter-manager]").val();
            tableCreditCap.ajax.reload();
        });

    });

</script>
@endpush