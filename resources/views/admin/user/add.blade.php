
@extends('layouts.admin.base')

@section('title', 'Add user')

@section('content')

    <div class="row">
        <div class="col-md-9">

            <div class="box box-default">
                <div class="box-header">
                    <a class="btn btn-info" href="{{ route('admin.user') }}" role="button">Show Users</a>
                </div>
                <div class="box-body">

                        <form class="form-horizontal" method="post" action="{{ route('admin.user.add') }}">

                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="inputName" class="col-sm-2 control-label">Name</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="name" placeholder="Enter Name" value="{{ old('name') }}" required>
                                </div>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-4">
                                    <input type="email" class="form-control" name="email" placeholder="Enter Email" value="{{ old('email') }}" required>
                                </div>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('google_folder') ? ' has-error' : '' }}">
                                <label for="inputGoogleFolder" class="col-sm-2 control-label">Google Folder ID</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="google_folder" placeholder="Enter Google Folder ID" value="{{ old('google_folder') }}">
                                </div>

                                @if ($errors->has('google_folder'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('google_folder') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('lt_id') ? ' has-error' : '' }}">
                                <label for="inputName" class="col-sm-2 control-label">LinkTrust ID</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="lt_id" placeholder="Enter LT Account ID" value="{{ old('lt_id') }}">
                                </div>

                                @if ($errors->has('lt_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('lt_id') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('ef_id') ? ' has-error' : '' }}">
                                <label for="inputName" class="col-sm-2 control-label">EverFlow ID</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="ef_id" placeholder="Enter EF Account ID" value="{{ old('ef_id') }}">
                                </div>

                                @if ($errors->has('ef_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('ef_id') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('role') ? ' has-error' : '' }}">
                                <label for="selectRole" class="col-sm-2 control-label">Role</label>
                                <div class="col-sm-2">
                                    <select class="form-control" name="role" required>
                                        <option value="">Please Set Role</option>
                                        @foreach($dataRole as $iter)
                                            <option value="{{ $iter->id }}" {{ old('role') == $iter->id ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if ($errors->has('role'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('role') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('show_for_manage_list') ? ' has-error' : '' }}">
                                <div class="col-sm-offset-2 col-sm-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                   name="show_for_manage_list"
                                                   value="1"
                                                    {{ old('show_for_manage_list') ? " checked" : "" }}>
                                            Show for Sales Managers in Drop Down lists
                                        </label>
                                    </div>
                                </div>

                                @if ($errors->has('show_for_manage_list'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('show_for_manage_list') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('show_for_account_manage_list') ? ' has-error' : '' }}">
                                <div class="col-sm-offset-2 col-sm-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                   name="show_for_account_manage_list"
                                                   value="1"
                                                    {{ old('show_for_account_manage_list') ? " checked" : "" }}>
                                            Show for Account Managers in Drop Down lists
                                        </label>
                                    </div>
                                </div>

                                @if ($errors->has('show_for_account_manage_list'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('show_for_account_manage_list') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('docusign_manager') ? ' has-error' : '' }}">
                                <div class="col-sm-offset-2 col-sm-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                   name="docusign_manager"
                                                   value="1"
                                                    {{ old('docusign_manager') ? " checked" : "" }}>
                                            Show for Docusign managers in Drop Down lists
                                        </label>
                                    </div>
                                </div>

                                @if ($errors->has('docusign_manager'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('docusign_manager') }}</strong>
                                    </span>
                                @endif
                            </div>

                            @csrf

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-1">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                                <div class="col-sm-1">
                                    <a href="{{ route('admin.user.add') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>

@endsection