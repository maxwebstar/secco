@extends('layouts.admin.base')

@section('title', 'Access')

@section('content')

    <div class="row">

        <div class="col-md-10">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Add</h3>
                </div>

                <form id="form-set-rate" class="" method="post" action="{{ route('admin.access.save') }}">
                    <div class="box-body">

                        <div class="row">
                            <div class="form-group col-sm-5 {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label">Name*</label>
                                <input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}" required>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-5 {{ $errors->has('label') ? ' has-error' : '' }}">
                                <label class="control-label">Label*</label>
                                <input type="text" class="form-control" name="label" placeholder="Label" value="{{ old('label') }}" required>

                                @if ($errors->has('label'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('label') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-5 {{ $errors->has('value') ? ' has-error' : '' }}">
                                <label class="control-label">Value*</label>
                                <input type="text" class="form-control" name="value" placeholder="Value" value="{{ old('value') }}" required>

                                @if ($errors->has('value'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('value') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3 {{ $errors->has('position') ? ' has-error' : '' }}">
                                <label class="control-label">Position*</label>
                                <input type="text" class="form-control" name="position" placeholder="Position" value="{{ old('position') }}" required>

                                @if ($errors->has('position'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('position') }}</strong>
                                    </span>
                                @endif
                            </div>
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

                        @csrf
                        <input type="hidden" name="id" value="">

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.access.index') }}" role="button">Cancel</a>
                    </div>
                </form>

            </div>

        </div>

    </div>

@endsection