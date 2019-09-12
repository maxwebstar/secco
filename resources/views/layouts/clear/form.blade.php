<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="{{asset('css/clear.css')}}">
    <link rel="stylesheet" href="{{asset('plugin/icheck/skins/square/blue.css')}}">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">

</head>
<body class="hold-transition login-page">

    @yield('content')

`   <script src="{{asset('js/clear.js')}}"></script>
    <script src="{{asset('plugin/icheck/icheck.min.js')}}"></script>

    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            });
        });
    </script>

</body>
</html>