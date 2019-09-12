@extends('layouts.admin.base')

@section('title', 'Cap Request')

@section('content')

    <div class="row">

        <div class="col-md-9">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">View</h3>
                </div>
                <div class="box-body">

                    @php

                        $offer = $data->offer;

                    @endphp

                    <table class="table table-striped table-io-view">
                        <thead>
                        <tr>
                            <th>Key</th><th>Value</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr><td>ID</td><td>{{ $data->id }}</td></tr>

                        <tr><td colspan="2"><strong>Campaign Information</strong></td></tr>
                        <tr><td class="param-label">LinkTrust Api</td><td>{{ $offer->need_api_lt ? 'Yes' : 'No' }}</td></tr>
                        <tr><td class="param-label">EverFlow Api</td><td>{{ $offer->need_api_ef ?  'Yes' : 'No' }}</td></tr>
                        <tr><td class="param-label">LinkTrust ID</td><td>{{ $offer->lt_id }}</td></tr>
                        <tr><td class="param-label">EverFlow ID</td><td>{{ $offer->ef_id }}</td></tr>
                        <tr><td class="param-label">EverFlow Status</td><td>{{ $offer->ef_status }}</td></tr>
                        <tr><td class="param-label">Campaign Name</td><td>{{ $offer->campaign_name }}</td></tr>

                        <tr><td colspan="2"></td></tr>
                        <tr><td>Effective Date</td><td>{{ date('M j, Y', strtotime($data->date )) }}</td></tr>
                        <tr><td>Cap New</td><td>{{ $data->cap }}</td></tr>
                        <tr><td>Cap Type</td><td>{{ $data->cap_type->name }}</td></tr>
                        <tr><td>Cap Reset</td><td>{{ $data->cap_reset ? "Yes" : "No" }}</td></tr>
                        <tr><td>Cap Redirect Link</td><td>{{ $data->redirect_url }}</td></tr>
                        <tr><td>Reason</td><td>{{ $data->reason }}</td></tr>

                        <tr><td></td><td></td></tr>
                        <tr><td>Status</td><td>{{ $data->getStatus() }}</td></tr>
                        <tr><td>Updated</td><td>{{ $data->updated_at ? date('M j, Y H:i:s', strtotime($data->updated_at)) : "" }}</td></tr>
                        <tr><td>Created</td><td>{{ date('M j, Y H:i:s', strtotime($data->created_at)) }}</td></tr>
                        <tr><td>Created By</td><td>{{ $data->created_param->name }}</td></tr>
                        <tr><td>Cron Error</td><td>{!! $data->error_cron !!}</td></tr>
                        <tr><td>Import From Old Dashboard</td><td>{{ $data->mongo_id ? "Yes" : "No" }}</td></tr>

                        </tbody>
                    </table>

                </div>
                <div class="box-footer">

                    @switch($data->status)
                        @case(1)
                            <a type="button" class="btn btn-success" href="/admin/request/cap/approve/{{ $data->id }}" onClick="return confirm('Approve ?')">Approve</a>
                            <button type="button" class="btn btn-danger" onClick="decline({{ $data->id }})">Decline</button>
                            <a type="button" class="btn btn-info" href="/admin/request/cap/edit/{{ $data->id }}">Edit</a>
                            @break
                        @case(2)
                            <a type="button" class="btn btn-success" href="/admin/request/cap/approve/{{ $data->id }}" onClick="return confirm('Approve ?')">Approve</a>
                            <a type="button" class="btn btn-info" href="/admin/request/cap/edit/{{ $data->id }}">Edit</a>
                            @break
                        @case(3)
                            @break
                    @endswitch

                    <a class="btn btn-default pull-right" href="{{ route('admin.request.cap.index') }}" role="button" style="margin-left: 5px;">Back to List</a>

                </div>

            </div>

        </div>

    </div>
@endsection

@push('fancybox')
<script src="{{ asset('js/admin/fancybox.js') }}"></script>
@endpush

@push('script')
<script>

    $(function(){

    });

    function decline(id)
    {
        if(confirm('Decline ?')) {

            $.fancybox.open({
                src: '/admin/request/cap/decline/'+id,
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
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                }
            });
        }
    }

</script>
@endpush