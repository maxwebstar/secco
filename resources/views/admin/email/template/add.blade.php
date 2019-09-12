@extends('layouts.admin.base')

@section('title', 'Email Template')

@section('content')

    <div class="row">

        <div class="col-md-9">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Add</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.email.template.save') }}">
                    <div class="box-body">

                        <div class="row">
                            <div class="form-group col-md-4 {{ $errors->has('group_id') ? ' has-error' : '' }}">
                                <label>Group*</label>
                                <select class="form-control" name="group_id" required>
                                    <option value="">Please select group</option>
                                    @foreach($dataGroup as $iter)
                                        <option value="{{ $iter->id }}" {{ old('group_id') == $iter->id ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('group_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('group_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="inputName" class="control-label">Name*</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Name" value="{{ old('name') }}" required>
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('display_name') ? ' has-error' : '' }}">
                            <label for="inputDisplayName" class="control-label">Display Name*</label>
                            <input type="text" class="form-control" name="display_name" placeholder="Enter Display Name" value="{{ old('display_name') }}" required>
                            @if ($errors->has('display_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('display_name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('to') ? ' has-error' : '' }}">
                            <label for="inputDescription" class="control-label">To
                            </label>
                            <textarea class="form-control" name="to" placeholder="address@mail.com, address2@mail.com ...">{{ old('to') }}</textarea>
                            <span class="help-block">You can enter many e-mail addresses separated by commas</span>
                            @if ($errors->has('to'))
                                <span class="help-block">
                                    <strong>{!! $errors->first('to') !!}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('from_name') ? ' has-error' : '' }}">
                                <label for="inputPosition" class="control-label">From name*</label>
                                <input type="text" class="form-control" name="from_name" placeholder="Enter From Name" value="{{ old('from_name') ? old('from_name') : config('mail.from.name') }}" required>

                                @if ($errors->has('from_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('from_name') }}</strong>
                                    </span>
                                @endif

                            </div>
                            <div class="form-group col-md-6 {{ $errors->has('from_email') ? ' has-error' : '' }}" style="width: 50%">
                                <label for="inputPosition" class="control-label">From email*</label>
                                <input type="text" class="form-control" name="from_email" placeholder="Enter From Email" value="{{ old('from_email') ? old('from_email') : config('mail.from.address') }}" required>

                                @if ($errors->has('from_email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('from_email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('subject') ? ' has-error' : '' }}">
                            <label for="inputPosition" class="control-label">Subject*</label>
                            <input type="text" class="form-control" name="subject" placeholder="Subject" value="{{ old('subject') }}" required>
                            @if ($errors->has('subject'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('subject') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('body') ? ' has-error' : '' }}">
                            <label for="inputDescription" class="control-label">Body*</label>
                            <textarea class="form-control tinymce-editor" rows="30" name="body" placeholder="">{!! old('body') !!}</textarea>
                            @if ($errors->has('body'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('body') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4 {{ $errors->has('status') ? ' has-error' : '' }}">
                                <label>Status*</label>
                                <select class="form-control" name="status" required>
                                    <option value="">Please select status</option>
                                    @foreach($arrStatus as $key => $iter)
                                        <option value="{{ $key }}" {{ old('status') == $key ? " selected" : "" }}>{{ $iter }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('status'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4 {{ $errors->has('position') ? ' has-error' : '' }}">
                                <label for="inputPosition" class="control-label">Position*</label>
                                <input type="text" class="form-control" name="position" placeholder="Enter Position" value="{{ old('position') }}" required>
                                @if ($errors->has('position'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('position') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="inputDescription" class="control-label">Description</label>
                            <textarea class="form-control" name="description" placeholder="Trigger, Indicator, Distribution, Other" rows="5">{{ old('description') ? old('description') : $data->getDefault() }}</textarea>

                            @if ($errors->has('description'))
                                <span class="help-block">
                                    <strong>{!! $errors->first('description') !!}</strong>
                                </span>
                            @endif
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.email.template') }}" role="button">Back to Emails templates</a>
                    </div>
                </form>

            </div>

        </div>

    </div>

@endsection

@push('tinymce')
<script src="{{ asset('js/admin/tinymce.js') }}"></script>
@endpush

@push('script')

    <script>
        $(function(){});
    </script>

@endpush