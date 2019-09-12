
@extends('layouts.clear.form')

@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('home') }}"><b>Admin</b> {{ config('app.name') }}</a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Please enter password for access</p>

            <form action="{{ route('admin.access.login.check') }}" method="post">

                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                @csrf

                <div class="row">
                    <!-- /.col -->
                    <div class="col-xs-6">
                        <a type="submit" class="btn btn-default btn-block btn-flat" href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
                    </div>
                    <div class="col-md-offset-1 col-xs-5">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>

            </form>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

@endsection