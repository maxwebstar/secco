@extends('layouts.admin.base')

@section('title', 'Advertiser Missing')

@section('content')

    <div class="row">

        <div class="col-md-9">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">View</h3>
                </div>
                <div class="box-body">

                    <table class="table table-striped table-io-view">
                        <thead>
                        <tr>
                            <th>Key</th><th>Value</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr><td>ID</td><td>{{ $data->id }}</td></tr>

                        <tr><td colspan="2"><strong>Advertiser Missing</strong></td></tr>

                        <tr><td>EverFlow ID</td><td>{{ $data->ef_id }}</td></tr>
                        <tr><td>EverFlow Status</td><td>{{ $data->ef_status }}</td></tr>
                        <tr><td>Name</td><td>{{ $data->name }}</td></tr>
                        <tr><td>Contact</td><td>{{ $data->contact }}</td></tr>
                        <tr><td>Email</td><td>{{ $data->email }}</td></tr>

                        <tr><td></td><td></td></tr>
                        <tr><td>Country</td><td>{{ $data->country_param->name }}</td></tr>
                        <tr><td>State</td><td>{{ $data->state_param->name }}</td></tr>
                        <tr><td>City</td><td>{{ $data->city }}</td></tr>
                        <tr><td>Street</td><td>{{ $data->street1 }}</td></tr>
                        <tr><td>Zip</td><td>{{ $data->zip }}</td></tr>

                        <tr><td></td><td></td></tr>
                        <tr><td>Sales Manager</td><td>{{ $data->manager->name }}</td></tr>
                        <tr><td>Account Manager</td><td>{{ $data->manager_account->name }}</td></tr>

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
                            <a type="button" class="btn btn-success" href="/admin/advertiser/add-missing/{{ $data->id }}">Add</a>
                            <a type="button" class="btn btn-danger" href="/admin/advertiser/ignore-missing/{{ $data->id }}" onClick="return confirm('Ignore ?')">Ignore</a>
                            @break
                        @case(2)
                            <a type="button" class="btn btn-success" href="/admin/advertiser/add-missing/{{ $data->id }}">Add</a>
                            @break
                        @case(3)
                            @break
                    @endswitch

                    <a class="btn btn-default pull-right" href="{{ route('admin.advertiser.missing') }}" role="button" style="margin-left: 5px;">Back to List</a>

                </div>

            </div>

        </div>

    </div>
@endsection