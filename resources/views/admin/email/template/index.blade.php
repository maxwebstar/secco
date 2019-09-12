@extends('layouts.admin.base')

@section('title', 'Emails Templates')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-11">

            <div class="box box-default">
                <div class="box-header">
                    <a class="btn btn-primary" href="{{ route('admin.email.template.add') }}" role="button">Create new Email Template</a>
                    <a class="btn btn-info" href="{{ route('admin.email.template.group') }}" role="button">Show Email Template Groups</a>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="email-template-table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Group</th>
                            <th>Display Name</th>
                            <th>Status</th>
                            <th>Template</th>
                            <th>Desc</th>
                            <th>Position</th>
                            <th>Group position</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $iter)
                            @php $group = $iter->template_group; @endphp
                            <tr>
                                <td>{{ $iter->name }}</td>
                                <td>{{ $group->display_name }}</td>
                                <td>{{ $iter->display_name }}</td>
                                <td>
                                    @switch($iter->status)
                                        @case(1)
                                            <i class="fa fa-exclamation-triangle fa-lg icon-yellow" data-toggle="tooltip" title="{{ $iter->getStatus() }}"></i>
                                            @break
                                        @case(2)
                                            <i class="fa fa-minus-circle fa-lg icon-red" data-toggle="tooltip" title="{{ $iter->getStatus() }}"></i>
                                            @break
                                        @case(3)
                                            <i class="fa fa-check-circle fa-lg icon-green" data-toggle="tooltip" title="{{ $iter->getStatus() }}"></i>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <i class="email-template cursor-pointer fa fa-eye fa-lg" href="#email-template-{{ $iter->id }}"></i>
                                    <div id="email-template-{{ $iter->id }}" style="display:none; width: 70%;">
                                        <h4>From:</h4>{{ $iter->from_name . "  " . $iter->from_email }}
                                        <h4>To:</h4>{!! $iter->getTo("string") !!}
                                        <br><br>
                                        <h4>Subject:</h4>{!! $iter->subject !!}
                                        <h4>Body:</h4>{!! $iter->body !!}
                                    </div>
                                </td>
                                <td>
                                    @if($iter->description)
                                        <i class="email-description cursor-pointer fa fa-eye fa-lg" href="#email-description-{{ $iter->id }}"></i>
                                        <div id="email-description-{{ $iter->id }}" style="display:none; width: 50%">
                                            {!! $iter->getDescription() !!}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $iter->position }}</td>
                                <td>{{ $group->position }}</td>
                                <td>
                                    <a type="button" class="btn btn-sm btn-info" href="{{ route('admin.email.template.edit', ['id' => $iter->id]) }}">Edit</a>
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

@push('fancybox')
<script src="{{ asset('js/admin/fancybox.js') }}"></script>
@endpush

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('script')
<script>
    $(document).ready(function() {

        jDT('#email-template-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: false,
            searching: true,
            ordering: true,
            pageLength: 50,
            order: [[6, 'asc']],
            columns: [
                {title: 'Name'},
                {title: 'Group'},
                {title: 'Display Name'},
                {title: 'Status', "className": "text-center"},
                {title: 'Template', "className": "text-center"},
                {title: 'Desc', "className": "text-center"},
                {title: 'Position', orderData: [7, 6]},
                {title: 'Group position', "targets": [7], "visible": false,},
                {title: 'Action', searchable: false, orderable: false,},
            ]
        });

        jQuery('[data-toggle="tooltip"]').tooltip();

        $(".email-template").fancybox({
            maxWidth	: 800,
            maxHeight	: 600,
            fitToView	: false,
            width	: '70%',
            height	: '70%',
            autoSize	: false,
            closeClick	: false,
            openEffect	: 'none',
            closeEffect	: 'none'
        });
        $(".email-description").fancybox({
            maxWidth	: 800,
            maxHeight	: 600,
            fitToView	: false,
            width	: '70%',
            height	: '70%',
            autoSize	: false,
            closeClick	: false,
            openEffect	: 'none',
            closeEffect	: 'none'
        });

    });

</script>
@endpush
