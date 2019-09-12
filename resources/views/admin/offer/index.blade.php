@extends('layouts.admin.base')

@section('title', 'Offer')

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

                    <table class="table table-bordered table-striped" id="offer-table">
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

@endsection

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('fancybox')
<script src="{{ asset('js/admin/fancybox.js') }}"></script>
@endpush

@push('script')
<script>

    var tableOffer = new Object();

    $(function() {

        var dataStatus = {!! json_encode($dataStatus) !!};

        var filterCreatedBy = $("select[name=filter-created-by]").val();
        var filterStatus = $("select[name=filter-status]").val();

        tableOffer = jDT('#offer-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('admin.offer.ajax.get') }}",
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

                        var html = '<a type="button" target="_blank" class="btn btn-sm btn-primary" href="/admin/offer/view/'+ full.id +'">View Data</a>&nbsp;';

                        switch(full.status){
                            case 1 : /*new*/
                                html += '<a type="button" class="btn btn-sm btn-success" href="/admin/offer/approve/'+ full.id +'" onClick="return confirm(\'Approve ?\')">Approve</a>&nbsp;';
                                html += '<button type="button" class="btn btn-sm btn-danger" onClick="decline('+full.id+')">Decline</button>&nbsp;';
                                html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="/admin/offer/edit-new/'+ full.id +'">Edit</a>&nbsp;';
                                break;
                            case 2 : /*declined*/
                                html += '<a type="button" class="btn btn-sm btn-success" href="/admin/offer/approve/'+ full.id +'">Approve</a>&nbsp;';
                                html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="/admin/offer/edit-new/'+ full.id +'">Edit</a>&nbsp;';
                                break;
                            case 3 : /*approved*/
                                html += '<a type="button" target="_blank" class="btn btn-sm btn-info" href="/admin/offer/edit/'+ full.id +'">Edit</a>&nbsp;';
                                break;
                        }

                        return html;
                    },
                },
            ]
        });

        $("#block-filter").on("change", "select[name=filter-status]", function() {

            filterStatus = $("select[name=filter-status]").val();
            tableOffer.ajax.reload();
        });
        $("#block-filter").on("change", "select[name=filter-created-by]", function() {

            filterCreatedBy = $("select[name=filter-created-by]").val();
            tableOffer.ajax.reload();
        });



    });

    function decline(id)
    {
        if(confirm('Decline ?')) {

            $.fancybox.open({
                src: '/admin/offer/decline/'+id,
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

                        tableOffer.ajax.reload();
                    }
                }
            });
        }
    }

</script>
@endpush