@extends('layouts.admin.base')

@section('title', 'Creative Missing')

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

                    <table class="table table-striped table-creative-missing-view">
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

                        <tr><td colspan="2"><strong>Creative</strong></td></tr>

                        <tr><td class="param-label">ID</td><td>{{ $data->id }}</td></tr>
                        <tr><td class="param-label">EF ID</td><td>{{ $data->ef_id }}</td></tr>
                        <tr><td class="param-label">Name</td><td>{{ $data->name }}</td></tr>
                        <tr><td class="param-label">Link</td><td>{{ $data->link }}</td></tr>
                        <tr><td class="param-label">In Price</td><td>{{ $data->price_in }}</td></tr>
                        <tr><td class="param-label">Out Price</td><td>{{ $data->price_out }}</td></tr>

                        <tr><td></td><td></td></tr>
                        <tr><td>Status</td><td>{{ $data->getStatus() }}</td></tr>
                        <tr><td>Updated</td><td>{{ $data->updated_at ? date('M j, Y H:i:s', strtotime($data->updated_at)) : "" }}</td></tr>
                        <tr><td>Created</td><td>{{ date('M j, Y H:i:s', strtotime($data->created_at)) }}</td></tr>

                        </tbody>
                    </table>

                </div>
                <div class="box-footer">

                    @switch($data->status)
                        @case(1)
                            <a type="button" class="btn btn-success" href="/admin/request/creative/add-missing/{{ $data->id }}" onClick="return confirm('Add ?')">Add</a>
                            <button type="button" class="btn btn-danger" href="/admin/request/creative/ignore-missing/{{ $data->id }}" onClick="return confirm('Ignore ?')">Ignore</button>
                            @break
                        @case(2)
                            <a type="button" class="btn btn-success" href="/admin/request/creative/approve/{{ $data->id }}" onClick="return confirm('Add ?')">Add</a>
                            @break
                        @case(3)
                            @break
                    @endswitch

                    <a class="btn btn-default pull-right" href="{{ route('admin.request.creative.missing') }}" role="button" style="margin-left: 5px;">Back to List</a>

                </div>

            </div>

        </div>

    </div>
@endsection

@push('script')
<script>

    $(function(){

    });

</script>
@endpush