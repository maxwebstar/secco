@extends('layouts.admin.base')

@section('title', 'QB Customer')

@section('content')

    <div class="row">

        <div class="col-md-9">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">View</h3>
                </div>
                <div class="box-body">

                    <table class="table table-striped table-qb-customer-view">
                        <thead>
                        <tr>
                            <th>Key</th><th>Value</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr><td>ID</td><td>{{ $data->id }}</td></tr>
                        <tr><td>QB ID</td><td>{{ $data->quickbook_id }}</td></tr>

                        @if($data->advertiser_id)

                            @php $advertiser = $data->advertiser; @endphp

                            <tr><td colspan="2"><strong>Advertiser Information</strong></td></tr>
                            <tr><td class="param-label">LinkTrust ID</td><td>{{ $advertiser->lt_id }}</td></tr>
                            <tr><td class="param-label">EverFlow ID</td><td>{{ $advertiser->ef_id }}</td></tr>
                            <tr><td class="param-label">Advertiser Name</td><td>{{ $advertiser->name }}</td></tr>
                        @endif

                        <tr><td colspan="2"></td></tr>
                        <tr><td>Name</td><td>{{ $data->name }}</td></tr>
                        <tr><td>Email</td><td>{{ $data->email }}</td></tr>
                        <tr><td>Phone</td><td>{{ $data->phone }}</td></tr>
                        <tr><td>Company</td><td>{{ $data->company }}</td></tr>
                        <tr><td>Active</td><td>{{ $data->active ? "Yes" : "No" }}</td></tr>

                        <tr><td></td><td></td></tr>
                        <tr><td>Status</td><td>{{ $data->getStatus() }}</td></tr>
                        <tr><td>Updated</td><td>{{ $data->updated_at ? date('M j, Y H:i:s', strtotime($data->updated_at)) : "" }}</td></tr>
                        <tr><td>Created</td><td>{{ date('M j, Y H:i:s', strtotime($data->created_at)) }}</td></tr>
                        <tr><td>Created QB</td><td>{{ date('M j, Y H:i:s', strtotime($data->created_qb)) }}</td></tr>

                        </tbody>
                    </table>

                </div>
                <div class="box-footer">

                    @switch($data->status)
                        @case(1)
                            <button type="button" class="btn btn-success" onClick="attacheAdvertiser({{ $data->id }})">Attach</button>
                            @break
                        @case(2)
                            <button type="button" class="btn btn-success" onClick="attacheAdvertiser({{ $data->id }})">Edit Attach</button>
                            @break
                    @endswitch

                    <a class="btn btn-default pull-right" href="{{ route('admin.qb.customer.index') }}" role="button" style="margin-left: 5px;">Back to List</a>

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

    function attacheAdvertiser(id)
    {
        $.fancybox.open({
            src: '/admin/qb/customer/attache/'+id,
            type : 'iframe',
            opts : {
                iframe : {
                    css : {
                        width: '70%',
                        height: '70%',
                    },
                    attr : {
                        scrolling : 'yes'
                    }
                },
                afterClose : function() {

                    location.reload();
                }
            }
        });
    }

</script>
@endpush