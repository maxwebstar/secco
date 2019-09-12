@extends('layouts.admin.base')

@section('title', 'Domains list')

@section('content')

    <div class="row">

        <div class="col-md-6">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Create</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.domain.save') }}">
                    <div class="box-body">

                        <div class="form-group {{ $errors->has('value') ? ' has-error' : '' }}">
                            <label for="inputName" class="control-label">Value</label>
                            <input type="text" class="form-control" name="value" placeholder="Value" value="{{ old('value') }}" required>
                            @if ($errors->has('value'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('value') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="inputDisplayName" class="control-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}" required>
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('position') ? ' has-error' : '' }}">
                            <label for="inputPosition" class="control-label">Position</label>
                            <input type="text" class="form-control" name="position" placeholder="Position" value="{{ old('position') }}" required>
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

                        @csrf
                        <input type="hidden" name="id" value="">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.domain') }}" role="button">Back to Domains list</a>
                    </div>
                </form>

            </div>

        </div>

    </div>

@endsection