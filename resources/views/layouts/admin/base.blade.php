<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }} @yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="{{asset('css/admin/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/custom.css')}}">
    @stack('css')

</head>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<!-- Site wrapper -->
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="../../index2.html" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>A</b>LT</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Admin</b>LTE</span>
        </a>

        @include('layouts.admin.inc.nav.top')

    </header>

    <!-- =============================================== -->

    @include('layouts.admin.inc.nav.left')

    <!-- =============================================== -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        @include('layouts.admin.inc.content.header')

        <!-- Main content -->
        <section class="content">

            @if (session()->has('message'))
                @component('layouts.partials.alert', ['type'=>session()->get('message')['type'], 'autohide'=>session()->get('message')['autohide']])
                    @slot('title')
                        {{ session()->get('message')['title'] }}
                    @endslot
                    {!! session()->get('message')['message'] !!}
                @endcomponent
            @endif

            @yield('content')

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    @include('layouts.admin.inc.footer')

    @include('layouts.admin.inc.nav.setting')

</div>
<!-- ./wrapper -->

<script src="{{asset('js/admin/app.js')}}"></script>
<script src="{{asset('js/admin/custom.js?v=c298c79457f33d')}}"></script>

@stack('fancybox')
@stack('datatable')
@stack('select2')
@stack('tinymce')
@stack('script')

</body>
</html>
