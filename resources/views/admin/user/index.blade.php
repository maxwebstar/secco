@extends('layouts.admin.base')

@section('title', 'Users')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')
    <div class="box box-default">
        <div class="box-header">
            <a class="btn btn-primary" href="{{ route('admin.user.add') }}" role="button">Create new user</a>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->getRoleParam("display_name") }}</td>
                            <td>{{ $user->getStatus() }}</td>
                            <td>{{ date("M d, Y", strtotime($user->created_at)) }}</td>
                            <td>
                                <a type="button" class="btn btn-sm btn-info" href="{{ route('admin.user.view', ['id' => $user->id]) }}">View</a>
                                @switch($user->status)
                                    @case(1)
                                        <a type="button" class="btn btn-sm btn-success" href="{{ route('admin.user.approve', ['id' => $user->id]) }}">Approve</a>
                                        <a type="button" class="btn btn-sm btn-danger" onclick="return confirm('Reject. Are you sure ?')" href="{{ route('admin.user.reject', ['id' => $user->id]) }}">Reject</a>
                                    @break
                                    @case(2)
                                        <a type="button" class="btn btn-sm btn-success" href="{{ route('admin.user.approve', ['id' => $user->id]) }}">Approve</a>
                                    @break
                                    @case(3)
                                        <a type="button" class="btn btn-sm btn-danger" onclick="return confirm('Reject. Are you sure ?')" href="{{ route('admin.user.reject', ['id' => $user->id]) }}">Reject</a>
                                    @break
                                @endswitch
                                <a type="button" class="btn btn-sm btn-danger" onclick="return confirm('Delete. Are you sure ?')" href="{{ route('admin.user.delete', ['id' => $user->id]) }}">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('script')
<script>
    $(function() {
        jDT('#users-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: false,
            searching: {{ $permission['user_search'] ? 1 : 0 }},
            ordering:  true,
            pageLength: 50,
            order: [[1, 'asc']],
            columns: [
                { title : 'ID' },
                { title : 'Name' },
                { title : 'Email' },
                { title : 'Role' },
                { title : 'Status' },
                { title : 'Created At' },
                { title : 'Action', width: "25%", searchable: false,  orderable: false,  },
            ]
        });
    });
</script>
@endpush