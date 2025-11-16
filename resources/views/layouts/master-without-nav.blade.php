<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-topbar="light">

    <head>
    <meta charset="utf-8" />
    <title>@yield('title') | {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="JCI COTE D'IVOIRE" name="description" />
    <meta content="AYIYIKOH" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="/images/favicon.png">
        @include('layouts.head-css')
       <!-- Include script -->
   {{-- {!! htmlScriptTagJsApi() !!} --}}
  </head>

    @yield('body')

    @yield('content')

    @include('layouts.vendor-scripts')
    </body>
</html>
