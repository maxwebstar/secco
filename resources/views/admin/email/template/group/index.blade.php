@extends('layouts.admin.base')

@section('title', 'Template Groups')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-9">

            <div class="box box-default">
                <div class="box-header">
                    <a class="btn btn-primary" href="{{ route('admin.email.template.group.add') }}" role="button">Create new Group</a>
                    <a class="btn btn-info" href="{{ route('admin.email.template') }}" role="button">Show Emails Templates</a>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="email-template-group-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Show</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $iter)
                            <tr>
                                <td>{{ $iter->id }}</td>
                                <td>{{ $iter->display_name }}</td>
                                <td>{{ $iter->position }}</td>
                                <td>{!! $iter->show ? '<i class="fa fa-check-circle fa-lg icon-green"></i>' : '<i class="fa fa-minus-circle fa-lg icon-red"></i>' !!}</td>
                                <td>{{ date("M d, Y", strtotime($iter->created_at)) }}</td>
                                <td>
                                    <a type="button" class="btn btn-sm btn-info" href="{{ route('admin.email.template.group.edit', ['id' => $iter->id]) }}">Edit</a>
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
        jDT('#email-template-group-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: false,
            searching: true,
            ordering:  true,
            pageLength: 50,
            order: [[2, 'asc']],
            columns: [
                { title : 'ID' },
                { title : 'Name' },
                { title : 'Position' },
                { title : 'Show', "className": "text-center" },
                { title : 'Created At' },
                { title : 'Action', searchable: false,  orderable: false,  },
            ]
        });
    });
</script>
@endpush