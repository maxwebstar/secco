@extends('layouts.admin.base')

@section('title', 'Terms and Conditions Template')

@section('content')

    <div class="row">

        <div class="col-md-9">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Add</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.term.template.save') }}">
                    <div class="box-body">

                        <div class="form-group {{ $errors->has('display_name') ? ' has-error' : '' }}">
                            <label for="inputDisplayName" class="control-label">Display Name*</label>
                            <input type="text" class="form-control" name="display_name" placeholder="Enter Display Name" value="{{ old('display_name') }}" required>
                            @if ($errors->has('display_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('display_name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('text') ? ' has-error' : '' }}">
                            <label for="inputDescription" class="control-label">Text</label>
                            <textarea class="form-control tinymce-editor" rows="30" name="text" placeholder="Enter Text">{!! old('text') !!}</textarea>
                            @if ($errors->has('text'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('text') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="inputDescription" class="control-label">Description</label>
                            <textarea class="form-control" name="description" placeholder="Enter Description" rows="5">{!! old('description') !!}</textarea>

                            @if ($errors->has('description'))
                                <span class="help-block">
                                    <strong>{!! $errors->first('description') !!}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('position') ? ' has-error' : '' }}">
                            <label for="inputPosition" class="control-label">Position*</label>
                            <input type="text" class="form-control" name="position" placeholder="Enter Position" value="{{ old('position') }}" required>
                            @if ($errors->has('position'))
                                <span class="help-block">
                                <strong>{{ $errors->first('position') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('show') ? ' has-error' : '' }}">
                            <div class="checkbox">
                                <label><input type="checkbox" name="show" value="1" {{ old('show') ? " checked" : "" }}><strong>Show</strong></label>
                            </div>
                            @if ($errors->has('show'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('show') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('by_default') ? ' has-error' : '' }}">
                            <div class="checkbox">
                                <label><input type="checkbox" name="by_default" value="1" {{ old('by_default') ? " checked" : "" }}><strong>By Default</strong></label>
                            </div>
                            @if ($errors->has('by_default'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('by_default') }}</strong>
                                </span>
                            @endif
                        </div>


                        @csrf
                        <input type="hidden" name="id" value="">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.term.template') }}" role="button">Back to T&C Templates</a>
                    </div>
                </form>

            </div>

        </div>

    </div>

@endsection

@push('tinymce')
{{--<script src="{{ asset('js/admin/tinymce.js') }}"></script>--}}
@endpush

@push('script')

<script>
    $(function(){});
</script>

@endpush