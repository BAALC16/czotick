<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-layout-style="" data-layout-position="fixed"  data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title> @yield('title')| {{ config('app.admin_name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="JCI COTE D'IVOIRE" name="description" />
    <meta content="AYIYIKOH" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="/images/favicon.png">
    @include('layouts.head-css')
</head>

{{-- @section('body')
    @include('layouts.body')
@show --}}
<body data-topbar="light">

    <!-- Begin page -->
    <div id="layout-wrapper">
 <body data-layout="horizontal">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        @include('layouts.horizontal')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <!-- Start content -->
                <div class="container-fluid">
                    @yield('content')
                </div> <!-- content -->
            </div>
            @include('layouts.footer')
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->
    @include('layouts.customizer')
    <!-- END Right Sidebar -->

    @include('layouts.vendor-scripts')
</body>

</html>
