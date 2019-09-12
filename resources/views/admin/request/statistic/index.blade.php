@extends('layouts.admin.base')

@section('title', 'Stat Request')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="box box-default">
                <div class="box-header">

                    <div id="block-ajax-request" class="box box-success box-solid" style="position: fixed; top: 60px; width: 93%; z-index: 10000; display: none;">
                        <div class="box-header">
                            <h3 class="box-title">Loading ...</h3>
                        </div>
                        <div class="box-body">Ajax request processing</div>
                        <div class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>

                </div>
                <div class="box-body">

                    <div id="block-filter">
                        <div class="row">
                            <div class="col-md-5">

                                <div class="form-group">
                                    <label></label>
                                    <select class="form-control" name="filter-manager">
                                        @if($auth->hasRole(['admin', 'ad_ops', 'accounting', 'account_manager']))
                                            <option value="0">All (Sales Manager)</option>
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
                                    <select class="form-control" name="filter-from-user">
                                            <option value="0">All (From User)</option>
                                            @foreach($dataFromUser as $iter)
                                                <option value="{{ $iter->id }}">{{ $iter->name }}</option>
                                            @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="filter-date" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="filter-notified">
                                            <strong>Show advertisers who will be notified</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="statistic-request-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>LT ID</th>
                            <th>EF ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Advertiser Email</th>
                            <th>Email</th>
                            <th>Revenue</th>
                            <th>Click</th>
                            <th>From User</th>
                            <th>Send</th>
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


    <div id="modal-notification" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Notification</h4>
                </div>
                <div class="modal-body">

                    <div id="block-modal-error"></div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="advertiser-ids" disabled="" value="">
                    </div>

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="advertiser-name" disabled="" value="">
                    </div>
                    <div class="form-group">
                        <label>Contact</label>
                        <input type="text" class="form-control" name="request-contact" placeholder="Enter Contact" disabled="" value="">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" name="request-email" placeholder="Enter Email" disabled="" value="">
                    </div>

                    <div class="form-group">
                        <label>From User</label>
                        <select class="form-control" name="from_user_id">
                            <option value="">Select User</option>
                            @foreach($dataFromUser as $iter)
                                <option value="{{ $iter->id }}">{{ $iter->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Send Stat Request Email</label>
                        <div class="radio" style="margin-top: 0px">
                            <label>
                                <input type="radio" id="notification-yes" name="notification" value="1">
                                Yes
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" id="notification-no" name="notification" value="0">
                                No
                            </label>
                        </div>
                    </div>
                    <div id="block-modal-reason" class="form-group" style="display: none;">
                        <label>Reason</label>
                        <textarea class="form-control" rows="2" name="reason" placeholder="Enter Reason"></textarea>
                    </div>

                    <input type="hidden" name="request-id" value="">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onClick="saveNotification()">Save</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@endsection

@push('css')

{{--<link rel="stylesheet" href="{{asset('css/admin/editor.bootstrap.css')}}">--}}
{{--<link rel="stylesheet" href="{{asset('css/admin/editor.dataTables.css')}}">--}}

<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>

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

{{--<script src="{{ asset('js/admin/editor.bootstrap.js') }}"></script>--}}
{{--<script src="{{ asset('js/admin/dataTables.editor.js') }}"></script>--}}

<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<script>

    var tableRequestStatistic = new Object();

    $(function() {

        var dataFromUserKey = {!! json_encode($dataFromUserKey) !!};

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

        var filterDate = $("input[name=filter-date]").val();
        var filterManager = $("select[name=filter-manager]").val();
        var filterFromUser = $("select[name=filter-from-user]").val();
        var filterNotified = $("input[name=filter-notified]").is(":checked") ? 1 : 0;

        tableRequestStatistic = jDT('#statistic-request-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.request.statistic.ajax.get') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    manager_id: function(){ return filterManager; },
                    from_user_id: function(){ return filterFromUser; },
                    date: function(){ return filterDate; },
                    notified: function(){ return filterNotified; },
                    _token: "{{ csrf_token() }}",
                }
            },
            drawCallback: function( settings ) {

                watchTable();
            },
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[3, 'desc']],
            columns: [
                {
                    title : 'ID',
                    data : 'id',
                    searchable: false,
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

                        var html = '<a class="advertiser-field-update" data-type="text" data-pk="'+full.id+'" data-name="advertiser_contact" data-url="/admin/request/statistic/ajax-save" data-title="Advertiser Contact">'+data+'</a>';

                        return html;
                    },
                },
                {
                    title : 'Advertiser Email',
                    data : 'email',
                    orderable: true,
                    visible: false,
                    searchable: false,
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Email',
                    data : 'advertiser_email',
                    orderable: true,
                    render: function (data, type, full, meta) {

                        if(data) {
                            var html = '<a class="advertiser-field-update" data-type="text" data-pk="' + full.id + '" data-name="advertiser_email" data-url="/admin/request/statistic/ajax-save" data-title="Advertiser Email">' + data + '</a>';
                        } else {
                            var html = '<a class="advertiser-field-update" data-type="text" data-pk="' + full.id + '" data-name="advertiser_email" data-url="/admin/request/statistic/ajax-save" data-title="Advertiser Email">' + 'enter value' + '</a>';
                        }

                        return html;
                    },
                },
                {
                    title : 'Revenue',
                    data : 'revenue',
                    searchable: false,
                    render: function (data, type, full, meta) {

                        return number_format(data, 2, ".", ",");
                    },
                },
                {
                    title : 'Clicks',
                    data : 'click',
                    searchable: false,
                    render: function (data, type, full, meta) {

                        return number_format(data, 0, ".", ",");
                    },
                },
                {
                    title : 'From User',
                    data : 'from_user_id',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {

                        if(data && typeof dataFromUserKey[data] !== "undefined"){
                            return dataFromUserKey[data];
                        } else {
                            return "";
                        }
                    },
                },
                {
                    title : 'Send',
                    data : 'notification',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {

                        var html = '';

                        if(data){
                            html = '<i class="fa fa-check-circle fa-lg icon-green"></i>';
                        } else {
                            html = '<i class="fa fa-minus-circle fa-lg icon-red"></i>';
                        }

                        return html;
                    },
                },
                {
                    title : 'Action',
                    searchable: false,
                    orderable: false,
                    data : '',
                    render: function (data, type, full, meta) {

                        var html = '<button type="button" class="btn btn-sm btn-info btn-edit-notification">Edit</button>';

                        return html;
                    },
                },
            ]
        });

        jDT('#statistic-request-table tbody').on('click', '.btn-edit-notification', function (){

            var dataRow = tableRequestStatistic.row( $(this).parents('tr') ).data();

            modalNotification(dataRow);
        });
        $('#modal-notification').on('hidden.bs.modal', function (){

            clearNotification();
        })

        $("#block-filter").on("change", "select[name=filter-manager]", function() {

            filterManager = $("select[name=filter-manager]").val();
            tableRequestStatistic.ajax.reload();
        });
        $("#block-filter").on("change", "select[name=filter-from-user]", function() {

            filterFromUser = $("select[name=filter-from-user]").val();
            tableRequestStatistic.ajax.reload();
        });
        $("#block-filter").on("change", "input[name=filter-date]", function() {

            filterDate = $("input[name=filter-date]").val();
            console.log(filterDate);
            tableRequestStatistic.ajax.reload();
        });
        $("#block-filter").on("change", "input[name=filter-notified]", function() {

            filterNotified = $("input[name=filter-notified]").is(":checked") ? 1 : 0;
            tableRequestStatistic.ajax.reload();
        });

    });

    function watchTable()
    {
        $('.advertiser-field-update').editable({
            emptytext: '',
            params: function(params) {  //params already contain `name`, `value` and `pk`

                var data = {};

                data['id'] = params.pk;
                data['value'] = params.value;
                data['field'] = params.name;
                data['_token'] = "{{ csrf_token() }}";

                return data;
            },
            success: function (response, newValue) {

                if (response.status === 'success') {

                } //msg will be shown in editable form
                if (response.status === 'error') {

                }

                /*jsAlertHtml.set(
                    response.alert.type,
                    response.alert.title,
                    response.alert.message,
                    1);
                $("section.content").prepend(jsAlertHtml.get());*/
            },
            error: function (response, newValue) {

                if (response.status === 'error') {

                    jsAlertHtml.set(
                        response.alert.type,
                        response.alert.title,
                        response.alert.message,
                        response.alert.hide);
                    $("section.content").prepend(jsAlertHtml.get());
                }
            }
        });
    }


    function modalNotification(dataRow)
    {
        var ids = 'DashbourdID='+dataRow.id+',  LT_ID='+dataRow.lt_id+',  EF_ID='+dataRow.ef_id;

        $('#modal-notification input[name=advertiser-ids]').val(ids);
        $('#modal-notification input[name=advertiser-name]').val(dataRow.name);
        $('#modal-notification input[name=request-contact]').val(dataRow.contact);
        $('#modal-notification input[name=request-email]').val(dataRow.email);
        $('#modal-notification select[name=from_user_id]').val((dataRow.from_user_id ? dataRow.from_user_id : ''));
        $('#modal-notification input[name=request-id]').val(dataRow.id);

        if(dataRow.notification){
            $('#notification-yes').prop('checked', true);
        } else {
            $('#notification-no').prop('checked', true);
            $('#modal-notification textarea[name=reason]').val(dataRow.reason);
        }

        $('#modal-notification').modal('show');

        $("#modal-notification").on("change", "input[name=notification]", function() {

            animateNotificationReason();
        });

        animateNotificationReason();
    }


    function clearNotification()
    {
        $('#modal-notification input[name=advertiser-ids]').val('');
        $('#modal-notification input[name=advertiser-name]').val('');
        $('#modal-notification input[name=request-contact]').val('');
        $('#modal-notification input[name=request-email]').val('');
        $('#modal-notification select[name=from_user_id]').val('');
        $('#modal-notification input[name=request-id]').val('');

        $('#notification-yes').prop('checked', false);
        $('#notification-no').prop('checked', false);

        $('#block-modal-reason').css('display', 'none');
        $('#modal-notification textarea[name=reason]').val('');
    }


    function animateNotificationReason()
    {
        if($("#notification-yes").is(":checked")){

            $('#block-modal-reason').css('display', 'none');
            $('#modal-notification textarea[name=reason]').val('');

        } else if ($("#notification-no").is(":checked")){

            $('#block-modal-reason').css('display', 'block');
        }
    }


    function saveNotification()
    {
        var id = $('#modal-notification input[name=request-id]').val();
        var from_user_id = $('#modal-notification select[name=from_user_id]').val();
        var notification = $('#modal-notification input[name=notification]:checked').val();
        var reason = $('#modal-notification textarea[name=reason]').val();

        $.ajax({
            url : "{{ route('admin.request.statistic.ajax.save.notification') }}",
            data : { id : id, from_user_id : from_user_id, notification : notification, reason : reason, _token: "{{ csrf_token() }}" },
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
            },
            success : function(response){

                switch(response.status){
                    case 'not_valid' :

                        var notValidMessage = '';
                        $.each(response.param, function(errorHolder, errorMassage){
                            $.each(errorMassage, function(key, message){
                                notValidMessage += '<p>'+message+'</p>';
                            });
                        });

                        jsAlertHtml.set(
                            'danger',
                            'Data is not valid.',
                            notValidMessage,
                            0);
                        $("#block-modal-error").prepend(jsAlertHtml.get());

                    case 'saved' :

                        /*jsAlertHtml.set(
                            response.alert.type,
                            response.alert.title,
                            response.alert.message,
                            1);
                        $("section.content").prepend(jsAlertHtml.get());*/

                        $('#modal-notification').modal('hide');

                        tableRequestStatistic.ajax.reload();

                        break;
                    case 'error' :

                        jsAlertHtml.set(
                            response.alert.type,
                            response.alert.title,
                            response.alert.message,
                            response.alert.hide);
                        $("#block-modal-error").prepend(jsAlertHtml.get());

                        break;
                    default :
                        break;
                }
            },
            error : function(response){

                jsAlertHtml.set(
                    response.alert.type,
                    response.alert.title,
                    response.alert.message,
                    response.alert.hide);
                $("#block-modal-error").prepend(jsAlertHtml.get());
            }
        });
    }


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