@extends('layouts.admin.base')

@section('title', 'IO')

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

                    <table class="table table-bordered table-striped" id="io-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Advertiser</th>
                            <th>Campaign Name</th>
                            <th>Status</th>
                            <th>Created By</th>
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

    <div id="modal-upload" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Upload signed document for</h4>
                </div>
                <div class="modal-body">
                    <div class="progress" style="display: none;">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                    </div>
                    <div id="select-file-block" class="dropzone">
                        <div class="dz-message needsclick">
                            <a class="btn btn-info" role="button">Select File</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="upload_io_id" value="">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection

@push('css')
<link rel="stylesheet" href="{{asset('css/admin/dropzone.min.css')}}">
@endpush

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('script')
<script src="{{ asset('js/admin/dropzone.js') }}"></script>
<script>

    Dropzone.autoDiscover = false;

    var tableIO = new Object();

    $(function() {

        var fileUUID = new Object();
        var fileUpload = new Object();
        var dataStatus = {!! json_encode($dataStatus) !!};

        var filterCreatedBy = $("select[name=filter-created-by]").val();
        var filterStatus = $("select[name=filter-status]").val();

        tableIO = jDT('#io-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.io.ajax.get') }}",
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
            order: [[5, 'desc']],
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
                    title : 'Campaign Name',
                    data : 'campaign_name',
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

                        html += '<a type="button" target="_blank" class="btn btn-sm btn-primary" href="/admin/io/view/'+ full.id +'">View Data</a>&nbsp;';
                        if(full.google_url) {
                            html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="' + full.google_url + '">View Document</a>&nbsp;';
                        }
                        switch(full.status){
                            case 1 : /*new*/
                                html += '<a type="button" class="btn btn-sm btn-success" href="/admin/io/approve/'+ full.id +'">Approve</a>&nbsp;';
                                html += '<a type="button" class="btn btn-sm btn-danger" href="/admin/io/decline/'+ full.id +'" onClick="return confirm(\'Decline ?\')">Decline</a>&nbsp;';
                                html += '<a type="button" class="btn btn-sm btn-success btn-io-upload" data-io="'+ full.id +'">Upload</a>&nbsp;';
                                break;
                            case 2 : /*declined*/
                                html += '<a type="button" class="btn btn-sm btn-success" href="/admin/io/approve/'+ full.id +'">Approve</a>&nbsp;';
                                break;
                            case 3 : /*approved*/
                                if(full.docusign_google_url) {
                                    html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="' + full.docusign_google_url + '">View Signed</a>&nbsp;';
                                }
                                break;
                            case 6 :
                                html += '<a type="button" id="btn-check-docusign-'+ full.id +'" class="btn btn-sm btn-success" data-loading-text="Loading..." onClick="checkDocusign('+ full.id +')">Check</a>&nbsp;';
                                break;
                        }

                        return html;
                    },
                },
            ]
        });


        $("#block-filter").on("change", "select[name=filter-status]", function() {

            filterStatus = $("select[name=filter-status]").val();
            tableIO.ajax.reload();
        });
        $("#block-filter").on("change", "select[name=filter-created-by]", function() {

            filterCreatedBy = $("select[name=filter-created-by]").val();
            tableIO.ajax.reload();
        });


        var dropzoneObj = $("#select-file-block").dropzone({

            url: "{!! route('admin.io.save.upload') !!}",
            paramName: 'file',
            acceptedFiles: 'application/pdf',
            addRemoveLinks: true,
            maxFiles: 1,
            sending: function(file, xhr, formData) {

                formData.append("id", $("input[name=upload_io_id]").val());
                formData.append("_token", "{{ csrf_token() }}");

                $('#modal-upload .progress').css('display', 'block');
            },
            totaluploadprogress: function (progress) {
                $('#modal-upload .progress-bar').css('width', progress + '%');
            },
            success: function(file, data){

                fileUUID[file.upload.uuid] = data.id;
                fileUpload = file;

                setTimeout(function(){ $('#modal-upload .progress').css('display', 'none'); }, 1500);
            },
            removedfile: function(file) {

                if(confirm('Do you want to delete?') == false) {
                    return false;
                } else {

                    if(typeof fileUUID[file.upload.uuid] !== 'undefined'){

                        $.ajax({
                            url : "{{ route('admin.io.delete.upload') }}",
                            data : { id : $("input[name=upload_io_id]").val(), _token : "{{ csrf_token() }}" },
                            async : true,
                            method : 'post',
                            dataType : 'json',
                            beforeSend : function (){
                            },
                            success : function(response){

                                file.previewElement.remove();

                                jsAlertHtml.set(
                                    response.alert.type,
                                    response.alert.title,
                                    response.alert.message,
                                    response.alert.hide);
                                $("section.content").prepend(jsAlertHtml.get());

                            },
                            error : function(response){

                                jsAlertHtml.set(
                                    response.alert.type,
                                    response.alert.title,
                                    response.alert.message,
                                    response.alert.hide);
                                $("section.content").prepend(jsAlertHtml.get());
                            }
                        });

                    } else {
                        file.previewElement.remove();
                    }

                    return true;
                }
            }

        });

        $("#io-table").on("click", ".btn-io-upload", function(event) {

            var elem = $(event.target);
            var id = $(elem).attr('data-io');

            $('input[name=upload_io_id]').val(id);
            $('#modal-upload .modal-header h4').html('Upload signed document for IO (ID:' + id + ')');

            jQuery('#modal-upload').modal('show');

        });

        jQuery('#modal-upload').on('hidden.bs.modal', function (){

            $('input[name=upload_io_id]').val('');
            $('#modal-upload .modal-header h4').html('');

            if(Object.keys(fileUpload).length){

                location.reload();
            }

        });

    });


    function checkDocusign(id)
    {
        $.ajax({
            url : "{{ route('admin.io.check.api.docusign') }}",
            data : { id : id, _token: "{{ csrf_token() }}" },
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
                jQuery('#btn-check-docusign-' + id).button('loading');
            },
            success : function(response){

                jQuery('#btn-check-docusign-' + id).button('reset');

                switch(response.status){
                    case 'ok' :

                        jsAlertHtml.set(
                            response.alert.type,
                            response.alert.title,
                            response.alert.message,
                            response.alert.hide);
                        $("section.content").prepend(jsAlertHtml.get());

                        tableIO.ajax.reload();

                        break;
                    case 'processing' :

                        jsAlertHtml.set(
                            response.alert.type,
                            response.alert.title,
                            response.alert.message,
                            response.alert.hide);
                        $("section.content").prepend(jsAlertHtml.get());
                        break;
                    case 'error' :

                        jsAlertHtml.set(
                            response.alert.type,
                            response.alert.title,
                            response.alert.message,
                            response.alert.hide);
                        $("section.content").prepend(jsAlertHtml.get());
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

                $("section.content").prepend(jsAlertHtml.get());
            }
        });
    }

</script>
@endpush