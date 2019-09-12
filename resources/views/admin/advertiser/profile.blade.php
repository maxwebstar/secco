@extends('layouts.admin.base')

@section('content')

    <div class="row">
        <div class="col-md-11">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Advertisers</h3>
                </div>

                <div class="box-body">

                    <div class="col-md-1">

                        <div class="form-group">

                            <select class="form-control" name="advertiser-network">
                                @foreach($dataNetwork as $iter)
                                    <option value="{{ $iter->field_name }}" {{ $iter->checkSelected() ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group">

                            <select class="form-control" name="search-advertiser">
                                <option></option>
                            </select>
                        </div>

                    </div>
                    <div class="col-md-5">
                        <a class="btn btn-primary pull-right" href="{{ route('admin.advertiser.add') }}" role="button">Add New Advertiser</a>
                        <a class="btn btn-warning pull-right" style="margin-right: 10px" href="{{ route('admin.io.individual') }}" role="button">Upload IO</a>
                        @if($data)
                            <a class="btn btn-info pull-right" style="margin-right: 10px" href="{{ route('admin.advertiser.edit', ['id' => $data->id]) }}" role="button">Edit Advertiser</a>
                            @if($data->manager_account_id)
                                <span class="help-block pull-right" style="margin-right: 10px"><strong>Account Manager </strong>{{ $data->manager_account->name }}</span>
                            @endif
                            <span class="help-block pull-right" style="margin-right: 10px"><strong>Sales Manager </strong>{{ $data->manager->name }}</span>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <span class="help-block">There are total <strong>{{ $count }}</strong> advertisers</span>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <br>

    @if($data)

    <div class="row">

        <div class="col-sm-2">
            <a style="cursor: pointer" onClick="tabAnimate('camp-total')" class="info-box bg-aqua dashboard-button">
                <span class="info-box-icon box-small-icon"><i class="fa fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Campaigns</span>
                    <span class="info-box-number"></span>
                </div>
            </a>
        </div>
        <div class="col-sm-2">
            <a style="cursor: pointer" onClick="tabAnimate('camp-live')" class="info-box bg-green dashboard-button">
                <span class="info-box-icon box-small-icon"><i class="fa fa-rss"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Live Campaigns</span>
                    <span class="info-box-number"></span>
                </div>
            </a>
        </div>
        <div class="col-sm-2">
            <a style="cursor: pointer" onClick="tabAnimate('camp-pause')" class="info-box bg-yellow dashboard-button">
                <span class="info-box-icon box-small-icon"><i class="fa fa-pause"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Paused Campaigns</span>
                    <span class="info-box-number"></span>
                </div>
            </a>
        </div>
        <div class="col-sm-2">
            <a style="cursor: pointer" onClick="tabAnimate('camp-dead')" class="info-box bg-red dashboard-button">
                <span class="info-box-icon box-small-icon"><i class="fa fa-minus-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Dead Campaigns</span>
                    <span class="info-box-number"></span>
                </div>
            </a>
        </div>
        <div class="col-sm-2">
            <a style="cursor: pointer" onClick="tabAnimate('io')" class="info-box bg-blue dashboard-button">
                <span class="info-box-icon box-small-icon"><i class="fa fa-files-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">View IO's</span>
                    <span class="info-box-number">{{ $dataIO->count() }}</span>
                </div>
            </a>
        </div>

    </div>


    <div class="row">

        <div class="col-md-8">
            <div class="box box-info box-solid">

                <div class="box-header with-border">
                    <h3 class="box-title">{{ $data->name . ($data->ef_id ? " (EF: $data->ef_id)" : "") . ($data->lt_id ? " (LT: $data->lt_id)" : "")}}</h3>
                </div>
                <div class="box-body">

                    <div id="tab-advertiser-graf" class="tab-advertiser" style="display: block;">1</div>
                    <div id="tab-advertiser-camp-total" class="tab-advertiser" style="display: none;">2</div>
                    <div id="tab-advertiser-camp-live" class="tab-advertiser" style="display: none;">3</div>
                    <div id="tab-advertiser-camp-pause" class="tab-advertiser" style="display: none;">4</div>
                    <div id="tab-advertiser-camp-dead" class="tab-advertiser" style="display: none;">5</div>
                    <div id="tab-advertiser-io" class="tab-advertiser" style="display: none;">

                        <div class="box box-solid">
                            <div class="box-body">
                                <h4 class="box-body-title">IO's for {{ $data->name }}</h4>

                                <table class="table table-bordered table-striped" id="advertiser-io-table">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Gov Date</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dataIO as $iter)
                                        <tr>
                                            <td>{{ $iter->google_file_name }}</td>
                                            <td>{{ $iter->gov_date ? date('M d, Y', strtotime($iter->gov_date)) : "" }}</td>
                                            <td>{{ $iter->google_created_at ? date('M d, Y', strtotime($iter->google_created_at)) : "" }}</td>
                                            <td>

                                                @php

                                                    if($iter->mongo_id){
                                                        $urlDoc = "https://docs.google.com/feeds/download/documents/export/Export?id=$iter->google_file&exportFormat=doc";
                                                    }else{
                                                        $urlDoc = "https://drive.google.com/uc?export=download&id=$iter->google_file";
                                                    }

                                                    if($iter->docusign_google_file){
                                                        $urlPdf = "https://drive.google.com/uc?export=download&id=$iter->docusign_google_file";
                                                    }

                                                @endphp

                                                @if($iter->google_url)
                                                    <a class="btn btn-primary btn-sm btn-margin-right" target="_blank" href="{{ $iter->google_url }}">View IO</a>

                                                    @if($iter->docusign_google_file)
                                                        <a class="btn btn-danger btn-sm btn-margin-right" href="{{ $urlPdf }}"><i class="fa fa-download"></i> Pdf</a>
                                                    @endif

                                                    <a class="btn btn-danger btn-sm btn-margin-right" href="{{ $urlDoc }}"><i class="fa fa-download"></i> Doc</a>

                                                    @if($iter->governing)
                                                        <button class="btn btn-success btn-sm btn-margin-right">Governing</button>
                                                    @else
                                                        <button class="btn btn-warning btn-sm btn-margin-right" onClick="setGovering({{ $iter->id }}, '{{ $iter->gov_date ? date('m/d/Y', strtotime($iter->gov_date)) : '' }}')">Set Governing</button>
                                                    @endif
                                                @endif

                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </div>

                </div>
                <div class="box-footer">
                </div>

            </div>
        </div>
        <div class="col-md-3">

            <div class="box box-info box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Stats Summary</h3>
                </div>
                <div class="box-body">

                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <td><strong>Total Clicks</strong></td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td><strong>Approved clicks</strong></td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td><strong>Total commission</strong></td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td><strong>Total revenue</strong></td>
                            <td>0</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="box-footer"></div>
            </div>
        </div>

    </div>

    @endif

    <div id="modal-set-gov-date" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Set Governing Date</h4>
                </div>
                <div class="modal-body">

                    <div class="alert alert-warning date-alert">Governing Date is missing for this IO </div>

                    <div class="form-group">
                        <label for="" class="">Date</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </span>
                            <input type="text" class="form-control" name="newGovDate" placeholder="mm/dd/yyyy">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm btn-saveGovDate">Save</button>
                    <button type="button" class="btn bg-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('select2')
<script src="{{ asset('js/admin/select2.js') }}"></script>
@endpush

@push('script')
<script>

    $(function() {

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
        $('select[name=search-advertiser]').on("select2:select", function (e){

            var id = $('select[name=search-advertiser]').val();

            window.location = "/admin/advertiser/dashboard/" + id;
        });

        @if($data)

            var tableIO = jDT('#advertiser-io-table').DataTable({
                autoWidth: false,
                processing: true,
                serverSide: false,
                searching: true,
                ordering: true,
                pageLength: 10,
                order: [[2, 'asc']],
                columns: [
                    {title: 'Name'},
                    {title: 'Gov Date', "className": "text-center"},
                    {title: 'Created', "className": "text-center"},
                    {title: 'Action', searchable: false, orderable: false,},
                ]
            });

        @endif

    });


    function tabAnimate(name)
    {
        $('.tab-advertiser').css('display', 'none');
        $('#tab-advertiser-' + name).css('display', 'block');
    }


    function setGovering(ioID, oldDate)
    {
        jQuery("input[name=newGovDate]").datepicker({
            formatDate : "mm/dd/yyyy",
            changeYear: true,
            changeMonth: true,
        });

        jQuery("#modal-set-gov-date").modal('show');

        if(oldDate){
            $('#modal-set-gov-date .date-alert').css('display', 'none');

            var tmpDate = new Date(oldDate);
            jQuery("input[name=newGovDate]").datepicker('setDate', tmpDate);

        } else {
            $('input[name=newGovDate]').val('');
            $('#modal-set-gov-date .date-alert').css('display', 'block');
        }

        $('#modal-set-gov-date').on('click', '.btn-saveGovDate', function(e){

            var date = $("input[name=newGovDate]").val();
            if(!date){
                $('#modal-set-gov-date .date-alert').css('display', 'block');
                return false;
            }
            var tmp = new Date(date);
            date = moment(tmp).format("YYYY-MM-DD");

            $.ajax({
                url : "{{ route('admin.ajax.save.io.gov.date') }}",
                data : { id : ioID, date : date, _token : "{{ csrf_token() }}" },
                async : true,
                method : 'post',
                dataType : 'json',
                beforeSend : function (){
                },
                success : function(response){

                    if(response.status == "ok"){

                        jsAlertHtml.set(
                            'success',
                            'Success!',
                            response.message,
                            0);

                        $("section.content").prepend(jsAlertHtml.get());

                        location.reload();

                    } else {

                        var html = "";
                        html += '<div class="alert alert-danger alert-dismissable">';
                        html +=     '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                        html +=  '<strong>Error!</strong> ' + response.message;
                        html += '</div>';

                        $("#modal-set-gov-date .modal-body").prepend(html);

                    }

                },
                error : function(response){

                    jsAlertHtml.set(
                        'danger',
                        'Error!',
                        'Something wrong please try again',
                        0);

                    $("section.content").prepend(jsAlertHtml.get());
                }
            });

        });

        jQuery("#modal-set-gov-date").on('hidden.bs.modal', function (){
            $("input[name=newGovDate]").val("");
            jQuery("input[name=newGovDate]").datepicker("destroy");
        });

    }

</script>
@endpush