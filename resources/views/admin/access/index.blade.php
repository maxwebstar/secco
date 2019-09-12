@extends('layouts.admin.base')

@section('title', 'Access')

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="box box-default">
                <div class="box-header">
                    <a class="btn btn-primary" href="{{ route('admin.access.add') }}" role="button">Add</a>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="permission-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Label</th>
                            <th>Value</th>
                            <th>Position</th>
                            <th>Show</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $iter)
                            <tr>
                                <td>{{ $iter->id }}</td>
                                <td>{{ $iter->name }}</td>
                                <td>{{ $iter->label }}</td>
                                <td>{{ $iter->value }}</td>
                                <td>{{ $iter->position }}</td>
                                <td>{!! $iter->show ? '<i class="fa fa-check-circle fa-lg icon-green"></i>' : '<i class="fa fa-minus-circle fa-lg icon-red"></i>' !!}</td>
                                <td>{{ date("M d, Y", strtotime($iter->created_at)) }}</td>
                                <td>
                                    <a type="button" class="btn btn-sm btn-info" href="{{ route('admin.access.edit', ['id' => $iter->id]) }}">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection