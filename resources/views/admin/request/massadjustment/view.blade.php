@extends('layouts.admin.base')

@section('title', 'MassAdjustment Request')

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
                        $affiliate = $data->affiliate;

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

                        <tr><td colspan="2"><strong>Affiliate Information</strong></td></tr>

                        <tr><td class="param-label">LinkTrust ID</td><td>{{ $affiliate->lt_id }}</td></tr>
                        <tr><td class="param-label">EverFlow ID</td><td>{{ $affiliate->ef_id }}</td></tr>
                        <tr><td class="param-label">LinkTrust Status</td><td>{{ $affiliate->lt_status }}</td></tr>
                        <tr><td class="param-label">EverFlow Status</td><td>{{ $affiliate->ef_status }}</td></tr>
                        <tr><td class="param-label">Name</td><td>{{ $affiliate->name }}</td></tr>

                        <tr><td colspan="2"></td></tr>
                        <tr><td>Effective Date</td><td>{{ date('M j, Y', strtotime($data->date )) }}</td></tr>
                        <tr><td>Clicks</td><td>{{ $data->click }}</td></tr>
                        <tr><td>Qualified</td><td>{{ $data->qualified }}</td></tr>
                        <tr><td>Approved</td><td>{{ $data->approved }}</td></tr>
                        <tr><td>Revenue</td><td>{{ $data->revenue }}</td></tr>
                        <tr><td>Commission</td><td>{{ $data->commission }}</td></tr>
                        <tr><td>Price Trend</td><td>{{ $data->getType() }}</td></tr>
                        <tr><td>Reason</td><td>{{ $data->reason }}</td></tr>

                        <tr><td></td><td></td></tr>
                        <tr><td>Status</td><td>{{ $data->getStatus() }}</td></tr>
                        <tr><td>Updated</td><td>{{ $data->updated_at ? date('M j, Y H:i:s', strtotime($data->updated_at)) : "" }}</td></tr>
                        <tr><td>Created</td><td>{{ date('M j, Y H:i:s', strtotime($data->created_at)) }}</td></tr>
                        <tr><td>Created By</td><td>{{ $data->created_param->name }}</td></tr>

                        </tbody>
                    </table>

                </div>
                <div class="box-footer">

                    @switch($data->status)
                    @case(1)
                    <a type="button" class="btn btn-success" href="/admin/request/massadjustment/approve/{{ $data->id }}" onClick="return confirm('Approve ?')">Approve</a>
                    <button type="button" class="btn btn-danger" onClick="decline({{ $data->id }})">Decline</button>
                    <a type="button" class="btn btn-info" href="/admin/request/massadjustment/edit/{{ $data->id }}">Edit</a>
                    @break
                    @case(2)
                    <a type="button" class="btn btn-success" href="/admin/request/massadjustment/approve/{{ $data->id }}" onClick="return confirm('Approve ?')">Approve</a>
                    <a type="button" class="btn btn-info" href="/admin/request/massadjustment/edit/{{ $data->id }}">Edit</a>
                    @break
                    @case(3)
                    @break
                    @endswitch

                    <a class="btn btn-default pull-right" href="{{ route('admin.request.mass.adjustment.index') }}" role="button" style="margin-left: 5px;">Back to List</a>

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
                src: '/admin/request/massadjustment/decline/'+id,
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