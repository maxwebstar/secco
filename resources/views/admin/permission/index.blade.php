@extends('layouts.admin.base')

@section('title', 'Permissions')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-11">

            <div class="box box-default">
                <div class="box-header">
                    <a class="btn btn-primary" href="{{ route('admin.permission.add') }}" role="button">Add Permission</a>
                    <a class="btn btn-info" href="{{ route('admin.permission.manage') }}" role="button">Show Manage permissions</a>
                    <a class="btn btn-info" href="{{ route('admin.permission.group') }}" role="button">Show Permission Groups</a>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="permission-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Alias</th>
                            <th>Group</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Group position</th>
                            <th>Show</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $iter)
                            @php $group = $iter->permissin_group; @endphp
                            <tr>
                                <td>{{ $iter->id }}</td>
                                <td>{{ $iter->name }}</td>
                                <td>{{ $group->display_name }}</td>
                                <td>{{ $iter->display_name }}</td>
                                <td>{{ $iter->position }}</td>
                                <td>{{ $group->position }}</td>
                                <td>{!! $iter->show ? '<i class="fa fa-check-circle fa-lg icon-green"></i>' : '<i class="fa fa-minus-circle fa-lg icon-red"></i>' !!}</td>
                                <td>{{ date("M d, Y", strtotime($iter->created_at)) }}</td>
                                <td>
                                    <a type="button" class="btn btn-sm btn-info" href="{{ route('admin.permission.edit', ['id' => $iter->id]) }}">Edit</a>
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

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('script')
<script>
    $(function() {
        jDT('#permission-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: false,
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[4, 'asc']],
            columns: [
                { title : 'ID' },
                { title : 'Alias' },
                { title : 'Group' },
                { title : 'Name' },
                { title : 'Position', orderData: [ 5, 4 ] },
                { title : 'Group position', "targets": [ 5 ], "visible": false, },
                { title : 'Show', "className": "text-center" },
                { title : 'Created At' },
                { title : 'Action', searchable: false,  orderable: false,  },
            ]
        });
    });
</script>
@endpush