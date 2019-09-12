@extends('layouts.admin.base')

@section('title', 'QB Report Month')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-11">

            <div class="box box-default">
                <div class="box-header">
                    <a class="btn btn-primary" href="{{ route('admin.credit.cap.index') }}" role="button">Back to CreditCap</a>
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
                            <div class="col-md-2 col-md-offset-1">
                                <div class="form-group">
                                    <label></label>
                                    <input class="form-control" type="text" name="filter-date" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label style="margin-top: 16px;">
                                            <input type="checkbox" name="filter-disable-date">
                                            <strong>Disable filter by Date</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="credit-cap-report-month-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Advertiser</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Date</th>
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
    .ui-datepicker-calendar {
        display: none;
    }
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

    var tableCreditCapReportMonth = new Object();

    $(function() {

        var dataType = {!! json_encode($data->arrType) !!};

        jQuery("input[name=filter-date]").datepicker({
            changeYear: true,
            changeMonth: true,
            showButtonPanel: true,
            formatDate : "MM yy",

            onClose: function(dateText, inst) {
                var month = jQuery("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = jQuery("#ui-datepicker-div .ui-datepicker-year :selected").val();
                jQuery(this).val(jQuery.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                jQuery(this).change();
            }
        });
        jQuery("input[name=filter-date]").datepicker('setDate', new Date());
        jQuery("input[name=filter-date]").val(jQuery.datepicker.formatDate('MM yy', new Date()))

        var filterManager = $("select[name=filter-manager]").val();
        var filterDate = $("input[name=filter-date]").val();
        var filterDisableDate = $("input[name=filter-disable-date]").is(":checked") ? 1 : 0;

        tableCreditCapReportMonth = jDT('#credit-cap-report-month-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.credit.cap.ajax.get.report.month') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    manager: function(){ return filterManager; },
                    date: function(){ return filterDate; },
                    _token: "{{ csrf_token() }}",
                }
            },
            createdRow: function(row, full, dataIndex){

            },
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[1, 'asc']],
            columns: [
                {
                    title : 'ID',
                    data : 'id',
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
                    title : 'Amount',
                    data : 'amount',
                    className : '',
                    render: function (data, type, full, meta) {

                        return number_format(data, 0, ".", ",");
                    },
                },
                {
                    title : 'Type',
                    data : 'type',
                    className : '',
                    searchable: false,
                    render: function (data, type, full, meta) {

                        var html = dataType[data];

                        return html;
                    },
                },
                {
                    title : 'Date',
                    data : 'date',
                    className : '',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                }
            ]
        });


        $("#block-filter").on("change", "select[name=filter-manager]", function() {

            filterManager = $("select[name=filter-manager]").val();
            tableCreditCapReportMonth.ajax.reload();
        });
        $("#block-filter").on("change", "input[name=filter-date]", function() {

            filterDate = $("input[name=filter-date]").val();
            tableCreditCapReportMonth.ajax.reload();
        });
        $("#block-filter").on("change", "input[name=filter-disable-date]", function() {

            filterDisableDate = $("input[name=filter-disable-date]").is(":checked") ? 1 : 0;
            if(filterDisableDate){
                $('input[name=filter-date]').prop('disabled', true);
                filterDate = "";
            } else {
                $('input[name=filter-date]').prop('disabled', false);
                filterDate = $("input[name=filter-date]").val();
            }
            tableCreditCapReportMonth.ajax.reload();
        });

    });


    function number_format(number, decimals, dec_point, thousands_sep)
    {

        // Format a number with grouped thousands
        //
        // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   bugfix by: Michael White (http://crestidg.com)

        var i, j, kw, kd, km;

        // input sanitation & defaults
        if( isNaN(decimals = Math.abs(decimals)) ){
            decimals = 2;
        }
        if( dec_point == undefined ){
            dec_point = ",";
        }
        if( thousands_sep == undefined ){
            thousands_sep = ".";
        }

        i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

        if( (j = i.length) > 3 ){
            j = j % 3;
        } else{
            j = 0;
        }

        km = (j ? i.substr(0, j) + thousands_sep : "");
        kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
        //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
        kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

        return km + kw + kd;
    }


</script>
@endpush