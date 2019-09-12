@extends('layouts.admin.base')

@section('title', 'Creative Missing')

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

                    <table class="table table-bordered table-striped" id="creative-missing-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>EF Api</th>
                            <th>Campaign Name</th>
                            <th>Name</th>
                            <th>EF Status</th>
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

@push('css')
<style type="text/css">

    /*td.details-control {*/
        /*background: url('/images/resources/details_open.png') no-repeat center center;*/
        /*cursor: pointer;*/
    /*}*/
    /*tr.details td.details-control {*/
        /*background: url('/images/resources/details_close.png') no-repeat center center;*/
    /*}*/

    /*.creative-list{ display: block; float: left; }*/
    /*.creative-name{ float: left; }*/
    /*.creative-link{ float: left; }*/
    /*.creative-price-in{ float: left; }*/
    /*.creative-price-out{ float: left; }*/
    /*.creative-lt-id{ float: left; }*/
    /*.creative-ef-id{ float: left; }*/

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

    var tableCreativeMissing = new Object();

    $(function() {

        var dataStatus = {!! json_encode($dataStatus) !!};

        var filterManager = $("select[name=filter-manager]").val();
        var filterStatus = $("select[name=filter-status]").val();

        tableCreativeMissing = jDT('#creative-missing-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.request.creative.ajax.get.missing') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    manager: function(){ return filterManager; },
                    status: function(){ return filterStatus; },
                    _token: "{{ csrf_token() }}",
                }
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
                    title : 'EF ID',
                    data : 'ef_id',
                    render: function (data, type, full, meta) {

                        return data;
                    },
                },
                {
                    title : 'Campaign Name',
                    data : 'campaign_name',
                    render: function (data, type, full, meta) {

                        return '<a target="_blank" href="/admin/offer/view/' + full.offer_id + '">' + data + '</a>';
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
                    title : 'EF Status',
                    data : 'ef_status',
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
                        html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="/admin/request/creative/view-missing/'+ full.id +'">View</a>&nbsp;';

                        switch(full.status){
                            case 1 : /*new*/
                                html += '<a type="button" class="btn btn-sm btn-success" href="/admin/request/creative/add-missing/'+ full.id +'" onClick="return confirm(\'Add ?\')">Add</a>&nbsp;';
                                html += '<a type="button" class="btn btn-sm btn-danger" href="/admin/request/creative/ignore-missing/'+ full.id +'" onClick="return confirm(\'Ignore ?\')">Ignore</a>&nbsp;';
                                if(full.can_attach) {
                                    html += '<a type="button" class="btn btn-sm btn-success" onClick="attacheCreative(' + full.id + ')">Attach</a>&nbsp;';
                                }
                                break;
                            case 2 : /*ignored*/
                                html += '<a type="button" class="btn btn-sm btn-success" href="/admin/request/creative/add-missing/'+ full.id +'" onClick="return confirm(\'Add ?\')">Add</a>&nbsp;';
                                if(full.can_attach) {
                                    html += '<a type="button" class="btn btn-sm btn-success" onClick="attacheCreative(' + full.id + ')">Attach</a>&nbsp;';
                                }
                                break;
                            case 3 : /*added*/
                                break;
                            case 4 : /*attached*/
                                html += '<a type="button" class="btn btn-sm btn-success" onClick="attacheCreative('+full.id+')">Edit Attached</a>&nbsp;';
                                break;
                        }

                        return html;
                    },
                },
            ]
        });


        $("#block-filter").on("change", "select[name=filter-status]", function() {

            filterStatus = $("select[name=filter-status]").val();
            tableCreativeMissing.ajax.reload();
        });
        $("#block-filter").on("change", "select[name=filter-manager]", function() {

            filterManager = $("select[name=filter-manager]").val();
            tableCreativeMissing.ajax.reload();
        });

//        // Array to track the ids of the details displayed rows
//        var detailRows = [];
//        // On each draw, loop over the `detailRows` array and show any child rows
//        tableRequestCreative.on( 'draw', function () {
//            $.each( detailRows, function ( i, id ) {
//                $('#'+id+' td.details-control').trigger( 'click' );
//            } );
//        } );
//        $('#creative-request-table tbody').on( 'click', 'tr td.details-control', function () {
//            var tr = $(this).closest('tr');
//            var row = tableRequestCreative.row( tr );
//
//            var idx = $.inArray( tr.attr('id'), detailRows );
//
//            if ( row.child.isShown() ) {
//                tr.removeClass( 'details' );
//                row.child.hide();
//
//                // Remove from the 'open' array
//                detailRows.splice( idx, 1 );
//            } else {
//                tr.addClass( 'details' );
//                row.child( createCreativeRow( row.data() ) ).show();
//
//                // Add to the 'open' array
//                if ( idx === -1 ) {
//                    detailRows.push( tr.attr('id') );
//                }
//            }
//        });

    });

    {{--function createCreativeRow( param ) {--}}

        {{--var dataCreativeStatus = {!! json_encode($dataCreativeStatus) !!};--}}
        {{--var div = $('<div/>').addClass('loading').text('Loading...');--}}

        {{--$.ajax({--}}
            {{--url: "{{ route('admin.ajax.get.creative.by.request') }}",--}}
            {{--data: { request_id: param.id, _token: "{{ csrf_token() }}" },--}}
            {{--async: true,--}}
            {{--method: 'post',--}}
            {{--dataType: 'json',--}}
            {{--beforeSend: function () {--}}

            {{--},--}}
            {{--success: function (response) {--}}

                {{--var html = '';--}}

                {{--if(response.creative) {--}}

                    {{--html += '<table class="table table-striped" style="margin-bottom: 0px;">';--}}
                    {{--html += '<tbody>';--}}

                    {{--html += '<tr>';--}}
                    {{--html += '<th>ID</th>';--}}
                    {{--html += '<th>LT ID</th>';--}}
                    {{--html += '<th>LT ID</th>';--}}
                    {{--html += '<th>Name</th>';--}}
                    {{--html += '<th>Link</th>';--}}
                    {{--html += '<th>Status</th>';--}}
                    {{--html += '</tr>';--}}


                    {{--$.each(response.creative, function (key, iter) {--}}

                        {{--if(typeof dataCreativeStatus[iter.status] !== "undefined"){--}}
                            {{--var status = dataCreativeStatus[iter.status];--}}
                        {{--} else {--}}
                            {{--var status = "";--}}
                        {{--};--}}

                        {{--html += '<tr>';--}}
                        {{--html += '<td>'+ iter.id +'</td>';--}}
                        {{--html += '<td>'+ iter.lt_id +'</td>';--}}
                        {{--html += '<td>'+ iter.ef_id +'</td>';--}}
                        {{--html += '<td>'+ iter.name +'</td>';--}}
                        {{--html += '<td>'+ iter.link +'</td>';--}}
                        {{--html += '<td>'+ status +'</td>';--}}
                        {{--html += '</tr>';--}}
                    {{--});--}}

                    {{--html += '</tbody>';--}}
                    {{--html += '</table>';--}}
                {{--}--}}

                {{--div.html(html).removeClass('loading');--}}
            {{--},--}}
            {{--error: function (response) {--}}

                {{--jsAlertHtml.set(--}}
                    {{--'danger',--}}
                    {{--'Error!',--}}
                    {{--'Something wrong please try again',--}}
                    {{--0);--}}

                {{--$("section.content").prepend(jsAlertHtml.get());--}}
            {{--}--}}
        {{--});--}}

        {{--return div;--}}
    {{--}--}}

    function attacheCreative(id)
    {
        $.fancybox.open({
            src: '/admin/request/creative/attach-missing/'+id,
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

                    tableCreativeMissing.ajax.reload();
                }
            }
        });
    }

</script>
@endpush