
@extends('layouts.admin.base')

@section('title', 'Profile')

@section('content')

    <div class="row">
        <div class="col-md-9">

            <div class="box box-default">
                <div class="box-header">

                </div>
                <div class="box-body">

                    <form class="form-horizontal" method="post" action="{{ route('admin.profile') }}">

                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="inputName" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') ? old ('name') : $data->name }}" required>
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
                                <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') ? old('email') : $data->email }}" required>
                            </div>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('google_folder') ? ' has-error' : '' }}">
                            <label for="inputName" class="col-sm-2 control-label">Google Folder ID</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="google_folder" placeholder="Google Folder ID" value="{{ $data->google_folder }}" disabled>
                            </div>

                            @if ($errors->has('google_folder'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('google_folder') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="inputRole" class="col-sm-2 control-label">Role</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" placeholder="" value="{{ $data->getRoleFirstPriority() }}" disabled>
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="inputOldPassword" class="col-sm-2 control-label" style="padding-top: 42px;">Password</label>
                            <div class="col-sm-4">
                                <span class="help-block">To change the password, please enter a current password.</span>
                                <input type="password" class="form-control" name="password" placeholder="current password" value="">

                            </div>

                            @if ($errors->has('password'))
                                <span class="help-block" style="padding-top: 34px;">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('password_new') ? ' has-error' : '' }}">
                            <label for="inputNewPassword" class="col-sm-2 control-label">New Password</label>
                            <div class="col-sm-4">
                                <input type="password" class="form-control" name="password_new" placeholder="new password" value="">
                            </div>

                            @if ($errors->has('password_new'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_new') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('password_new_confirmation') ? ' has-error' : '' }}">
                            <label for="inputNewPassword" class="col-sm-2 control-label">New Password</label>
                            <div class="col-sm-4">
                                <input type="password" class="form-control" name="password_new_confirmation" placeholder="new password confirmation" value="">
                            </div>

                            @if ($errors->has('password_new_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_new_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>

                        @csrf

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-4">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a class="btn btn-info pull-right" href="{{ route('admin.userparamemail.edit') }}" role="button">Email Params</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection