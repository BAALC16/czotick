<!doctype html>
<html lang="fr">
  <head>
      <base href="{{ url('') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ config('app.name') }} - L'immobilier en version digitale">
    <meta name="author" content="MCK">
    <title>@section('title') L'Outil Digital de Gestion Immobili&egrave;re en C&ocirc;te-d'Ivoire @show - {{ config('app.name') }}</title>
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
      rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <!-- Vendors CSS -->
    <link href="{{ asset('vendors/stylesearchbar.css') }}" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="/vendors/fontawesome-pro-5/css/all.css" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <link rel="stylesheet" href="/vendors/bootstrap-select/css/bootstrap-select.min.css" rel="preload" as="style">
    <link rel="stylesheet" href="/vendors/slick/slick.min.css" rel="preload" as="style">
    <link rel="stylesheet" href="/vendors/magnific-popup/magnific-popup.min.css" rel="preload" as="style">
    <link rel="stylesheet" href="/vendors/jquery-ui/jquery-ui.min.css" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <!--link rel="stylesheet" href="/vendors/chartjs/Chart.min.css" rel="preload" as="style"-->
    <link rel="stylesheet" href="/vendors/dropzone/css/dropzone.min.css" rel="preload" as="style">
    <link rel="stylesheet" href="/vendors/animate.css" rel="preload" as="style">
    <link rel="stylesheet" href="/vendors/timepicker/bootstrap-timepicker.min.css" rel="preload" as="style">
    <!--link rel="stylesheet" href="/vendors/mapbox-gl/mapbox-gl.min.css" rel="preload" as="style"-->
    <link rel="stylesheet" href="/vendors/dataTables/jquery.dataTables.min.css" rel="preload" as="style">
    <link rel="stylesheet" type="text/css" href="/vendors/waitMe/waitMe.min.css" rel="preload" as="style">
    <!-- Themes core CSS -->
    <link rel="stylesheet" href="/css/themes.css" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <!-- Icons Css -->
    <!--link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" /-->
    @yield('specific-css')

      <style>
          .wishlist-added i{
              color: #ff5951 !important;
              /*background: #ff5951 !important;*/
          }
          .wishlist-added i:hover{
              color: #ff5951  !important;
              /*background: white !important;*/
          }
      </style>

    <!-- Favicons -->
    <link rel="icon" href="/images/favicon.ico">
    @section('twitter-meta')
      <!-- Twitter -->
      <meta name="twitter:card" content="summary">
      <meta name="twitter:site" content="@">
      <meta name="twitter:creator" content="@">
      <meta name="twitter:title" content="Ayiyikoh">
      <meta name="twitter:description" content="L'Outil Digital de Gestion Immobili&egrave;re en C&ocirc;te-d'Ivoire">
      <meta name="twitter:image" content="/images/homeid-social-logo.png">
    @show
    @section('og-meta')
      <!-- Facebook -->
      <meta property="og:url" content="/">
      <meta property="og:title" content="Ayiyikoh">
      <meta property="og:description" content="L'Outil Digital de Gestion Immobili&egrave;re en C&ocirc;te-d'Ivoire">
      <meta property="og:type" content="website">
      <meta property="og:image" content="/images/favicon.ico">
      <meta property="og:image:type" content="image/png">
      <meta property="og:image:width" content="32">
      <meta property="og:image:height" content="63320">
    @show
  </head>
  <body>
    @section('menu')
      @include('public.partials.menu')
    @show
    <main id="content">

      @yield('content')

    </main>
    @section('footer')
      @include('public.partials.footer')
    @show
    <!-- Vendors scripts -->
    <script src="/vendors/jquery.min.js"></script>
    <script src="/vendors/jquery-ui/jquery-ui.min.js"></script>
    <script src="/vendors/bootstrap/bootstrap.bundle.js" async></script>
    <script src="/vendors/bootstrap-select/js/bootstrap-select.min.js" async></script>
    <script src="/vendors/slick/slick.min.js" async></script>
    <script src="/vendors/waypoints/jquery.waypoints.min.js" async></script>
    <script src="/vendors/counter/countUp.js" async></script>
    <script src="/vendors/magnific-popup/jquery.magnific-popup.min.js" async></script>
    <!--script src="/vendors/chartjs/Chart.min.js" async></script-->
    <script src="/vendors/dropzone/js/dropzone.min.js" async></script>
    <script src="/vendors/timepicker/bootstrap-timepicker.min.js" async></script>
    <script src="/vendors/hc-sticky/hc-sticky.min.js" async></script>
    <!--script src="/vendors/jparallax/TweenMax.min.js" async></script-->
    <script src="/vendors/dataTables/jquery.dataTables.min.js" async></script>
    <script src="/vendors/waitMe/waitMe.min.js" type="text/javascript" charset="utf-8" async></script>
    <script src="/vendors/sweetalert2.all.min.js" async></script>
    <script src="/vendors/config.js" async></script>
    <!-- Theme scripts -->
    <script src="/js/theme.js" async></script>
    <script src="/js/application.js" async></script>

    @yield('specific-js')

    @section('auth-modals')
      @include('public.partials.auth-modal')
    @show

    @include('public.partials.scroll-top')
  </body>
</html>
