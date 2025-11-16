<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="MCK">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@section('title') Administration @show - {{ config('app.name') }}</title>
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet">
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/vendors/fontawesome-pro-5/css/all.css">
    <link rel="stylesheet" href="/vendors/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="/vendors/slick/slick.min.css">
    <link rel="stylesheet" href="/vendors/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="/vendors/animate.css">
    <link rel="stylesheet" type="text/css" href="/vendors/waitMe/waitMe.min.css">

    {{-- <link rel="stylesheet" href="/vendors/magnific-popup/magnific-popup.min.css">
    <link rel="stylesheet" href="/vendors/chartjs/Chart.min.css">
    <link rel="stylesheet" href="/vendors/dropzone/css/dropzone.min.css">
    <link rel="stylesheet" href="/vendors/timepicker/bootstrap-timepicker.min.css">
    <link rel="stylesheet" href="/vendors/mapbox-gl/mapbox-gl.min.css">
    <link rel="stylesheet" href="/vendors/dataTables/jquery.dataTables.min.css"> --}}

    <!-- Themes core CSS -->
    <link rel="stylesheet" href="/css/themes.css">
    @yield('specific-css')
    <!-- Favicons -->
    <link rel="icon" href="/images/favicon.png">
  </head>
  <body>
    <div class="wrapper dashboard-wrapper">
      <div class="d-flex flex-wrap flex-xl-nowrap">
        <div class="db-sidebar bg-white">
          <nav class="navbar navbar-expand-xl navbar-light d-block px-0 header-sticky dashboard-nav py-0">
            <div class="sticky-area shadow-xs-1 py-3">
              <div class="d-flex px-3 px-xl-6 w-100">
                <a class="navbar-brand" href="{{route('backoffice')}}">
                  <img src="/images/logo.png" alt="{{ config('app.name') }}">
                </a>
                <div class="ml-auto d-flex align-items-center ">
                  <div class="d-flex align-items-center d-xl-none">
                    <div class="dropdown px-3">
                      <a href="#" class="dropdown-toggle d-flex align-items-center text-heading"
                           data-toggle="dropdown">
                        <div class="w-48px">
                          <img src="/images/testimonial-5.jpg"
                                     alt="Ronald Hunter" class="rounded-circle">
                        </div>
                        <span class="fs-13 font-weight-500 d-none d-sm-inline ml-2">
                          Ronald Hunter
                        </span>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#">My Profile</a>
                        <a class="dropdown-item" href="#">My Profile</a>
                        <a class="dropdown-item" href="#">Logout</a>
                      </div>
                    </div>
                    <div class="dropdown no-caret py-4 px-3 d-flex align-items-center notice mr-3">
                      <a href="#" class="dropdown-toggle text-heading fs-20 font-weight-500 lh-1"
                           data-toggle="dropdown">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-primary badge-circle badge-absolute font-weight-bold fs-13">1</span>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                      </div>
                    </div>
                  </div>
                  <button class="navbar-toggler border-0 px-0" type="button" data-toggle="collapse"
                        data-target="#primaryMenuSidebar"
                        aria-controls="primaryMenuSidebar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>
                </div>
              </div>
              <div class="collapse navbar-collapse bg-white" id="primaryMenuSidebar">
                <form class="d-block d-xl-none pt-5 px-3">
                  <div class="input-group">
                    <div class="input-group-prepend mr-0 bg-input">
                      <button class="btn border-0 shadow-none fs-20 text-muted pr-0" type="submit"><i
                                class="far fa-search"></i></button>
                    </div>
                    <input type="text" class="form-control border-0 form-control-lg shadow-none"
                           placeholder="Search for..." name="search">
                  </div>
                </form>
                <ul class="list-group list-group-flush w-100">
                  <li class="list-group-item pt-6 pb-4">
                    <!--h5 class="fs-13 letter-spacing-087 text-muted mb-3 text-uppercase px-3">Aller à</h5-->
                    <ul class="list-group list-group-no-border rounded-lg">
                      <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
                        <a href="{{route('backoffice')}}" class="text-heading lh-1 sidebar-link">
                          <span class="sidebar-item-icon d-inline-block mr-3 fs-20"><i
                                        class="fal fa-cog"></i></span>
                          <span class="sidebar-item-text">Tableau de bord</span>
                        </a>
                      </li>
                    </ul>
                  </li>

                  @section('side-menu')
                    @include('backoffice.partials.side-menu')
                  @show

                </ul>
              </div>
            </div>
          </nav>
        </div>
        <div class="page-content">

          @section('header')
            @include('backoffice.partials.header')
          @show

          <main id="content" class="bg-gray-01">

            @yield('content')

          </main>
        </div>
      </div>
    </div>

    @section('footer')
      @include('backoffice.partials.footer')
    @show

    <!-- Vendors scripts -->
    <script src="/vendors/jquery.min.js"></script>
    <script src="/vendors/jquery-ui/jquery-ui.min.js"></script>
    <script src="/vendors/bootstrap/bootstrap.bundle.js"></script>
    <script src="/vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="/vendors/slick/slick.min.js"></script>
    <script src="/vendors/waypoints/jquery.waypoints.min.js"></script>
    <script src="/vendors/hc-sticky/hc-sticky.min.js"></script>
    <script src="/vendors/waitMe/waitMe.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/vendors/sweetalert2.all.min.js"></script>

    {{-- <script src="/vendors/counter/countUp.js"></script>
    <script src="/vendors/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="/vendors/chartjs/Chart.min.js"></script>
    <script src="/vendors/dropzone/js/dropzone.min.js"></script>
    <script src="/vendors/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="/vendors/jparallax/TweenMax.min.js"></script>
    <script src="/vendors/mapbox-gl/mapbox-gl.js"></script>
    <script src="/vendors/dataTables/jquery.dataTables.min.js"></script> --}}

    <!-- Theme scripts -->
    <script src="/js/theme.js"></script>
    <script src="/vendors/config.js"></script>
    <script src="/js/backoffice.js"></script>

    @yield('specific-js')

  </body>
</html>
