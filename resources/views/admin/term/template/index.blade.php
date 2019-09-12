@extends('layouts.admin.base')

@section('title', 'Terms and Conditions Templates')

{{--@section('breadcrumbs', Breadcrumbs::render('style'))--}}

@section('content')

    <div class="row">
        <div class="col-md-11">

            <div class="box box-default">
                <div class="box-header">
                    <a class="btn btn-primary" href="{{ route('admin.term.template.add') }}" role="button">Create new T&C Template</a>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="term-template-table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Template</th>
                            <th>Description</th>
                            <th>Position</th>
                            <th>By Default</th>
                            <th>Show</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $iter)
                            <tr>
                                <td>{{ $iter->display_name }}</td>
                                <td>
                                    @if($iter->text)
                                        <i class="term-template cursor-pointer fa fa-eye fa-lg" href="#term-template-{{ $iter->id }}"></i>
                                        <div id="term-template-{{ $iter->id }}" style="display:none; width: 70%;">
                                            {!! $iter->text !!}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($iter->description)
                                        <i class="term-description cursor-pointer fa fa-eye fa-lg" href="#term-description-{{ $iter->id }}"></i>
                                        <div id="term-description-{{ $iter->id }}" style="display:none; width: 50%">
                                            {!! $iter->description !!}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $iter->position }}</td>
                                <td>{!! $iter->by_default ? '<i class="fa fa-check-circle fa-lg icon-green"></i>' : '<i class="fa fa-minus-circle fa-lg icon-red"></i>' !!}</td>
                                <td>{!! $iter->show ? '<i class="fa fa-check-circle fa-lg icon-green"></i>' : '<i class="fa fa-minus-circle fa-lg icon-red"></i>' !!}</td>
                                <td>
                                    <a type="button" class="btn btn-sm btn-info" href="{{ route('admin.term.template.edit', ['id' => $iter->id]) }}">Edit</a>
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

        jDT('#term-template-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: false,
            searching: true,
            ordering: true,
            pageLength: 50,
            order: [[3, 'asc']],
            columns: [
                {title: 'Name'},
                {title: 'Template', "className": "text-center"},
                {title: 'Description', "className": "text-center"},
                {title: 'Position', "className": "text-center"},
                {title: 'By Default', "className": "text-center"},
                {title: 'Show', "className": "text-center"},
                {title: 'Action', searchable: false, orderable: false,},
            ]
        });

        jQuery('[data-toggle="tooltip"]').tooltip();

        $(".term-template").fancybox({
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
        $(".term-description").fancybox({
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