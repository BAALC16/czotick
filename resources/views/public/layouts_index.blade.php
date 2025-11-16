<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>

<!-- Meta Tags -->
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="description" content="NIRVANA" />
<meta name="keywords" content="" />
<meta name="author" content="" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Page Title -->
<title>Nirvana</title>

<!-- Favicon and Touch Icons -->
<link href="{{ URL::asset('assets/front/images/apple-touch-icon-72x72.png') }}" rel="shortcut icon" type="image/png">
<link href="{{ URL::asset('assets/front/images/apple-touch-icon.png') }}" rel="apple-touch-icon">
<link href="{{ URL::asset('assets/front/images/apple-touch-icon-72x72.png') }}" rel="apple-touch-icon" sizes="72x72">
<link href="{{ URL::asset('assets/front/images/apple-touch-icon-114x114.png') }}" rel="apple-touch-icon" sizes="114x114">
<link href="{{ URL::asset('assets/front/images/apple-touch-icon-144x144.png') }}" rel="apple-touch-icon" sizes="144x144">

<!-- Stylesheet -->
<link href="{{ URL::asset('assets/front/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/front/css/animate.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/front/css/javascript-plugins-bundle.css') }}" rel="stylesheet"/>

<!-- CSS | menuzord megamenu skins -->
<link href="{{ URL::asset('assets/front/js/menuzord/css/menuzord.css') }}" rel="stylesheet"/>

<!-- CSS | Main style file -->
<link href="{{ URL::asset('assets/front/css/style-main.css') }}" rel="stylesheet" type="text/css">
<link id="menuzord-menu-skins" href="{{ URL::asset('assets/front/css/menuzord-skins/menuzord-rounded-boxed.css') }}" rel="stylesheet"/>

<!-- CSS | Responsive media queries -->
<link href="{{ URL::asset('assets/front/css/responsive.css') }}" rel="stylesheet" type="text/css">
<!-- CSS | Style css. This is the file where you can place your own custom css code. Just uncomment it and use it. -->

<!-- CSS | Theme Color -->
<link href="{{ URL::asset('assets/front/css/colors/theme-skin-color-set1.css') }}" rel="stylesheet" type="text/css">

<!-- external javascripts -->
<script src="{{ URL::asset('assets/front/js/jquery.js') }}"></script>
<script src="{{ URL::asset('assets/front/js/popper.min.js') }}"></script>
<script src="{{ URL::asset('assets/front/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('assets/front/js/javascript-plugins-bundle.js') }}"></script>
<script src="{{ URL::asset('assets/front/js/menuzord/js/menuzord.js') }}"></script>

<!-- REVOLUTION STYLE SHEETS -->
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/front/js/revolution-slider/css/rs6.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/front/js/revolution-slider/extra-rev-slider1.css') }}">
<!-- REVOLUTION LAYERS STYLES -->
<!-- REVOLUTION JS FILES -->
<script src="{{ URL::asset('assets/front/js/revolution-slider/js/revolution.tools.min.js') }}"></script>
<script src="{{ URL::asset('assets/front/js/revolution-slider/js/rs6.min.js') }}"></script>
<script src="{{ URL::asset('assets/front/js/revolution-slider/extra-rev-slider1.js') }}"></script>

<!-- <link href="css/style.css" rel="stylesheet" type="text/css"> -->
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js') }}"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js') }}"></script>
<![endif]-->
<style>

    @font-face {
        font-family: 'HelveticaNeueHeavy';
        src: url('/assets/fonts/helvetica-neue-5/HelveticaNeueRoman.otf') format('opentype');
    }

    * {
        font-family: 'HelveticaNeueHeavy' !important;
    }

    .fa, .fas {
        font-family: 'Font Awesome 5 Free' !important;
    }

    .fab {
        font-family: 'Font Awesome 5 Brands' !important;
    }

    .lg-icon {
        font-family: 'lg' !important;
    }

    .far {
        font-family: 'Font Awesome 5 Free' !important;
    }

    .page-link{
        font-family: "Open Sans", sans-serif !important;
    }

    
    @media (max-width: 767.98px) { /* small devices (landscape phones, less than 768px) */
        .event11 {
            display: none !important;
        }

        .custom-hide{
          padding-top: 0px !important;
        }
    }

    @media (min-width: 767.98px) { /* small devices (landscape phones, less than 768px) */

        .custom-hide{
           padding-top: 60px !important;
        }
    }

    @media (min-width: 570px) { /* small devices (landscape phones, less than 768px) */

        #cadre{
            margin: 3rem !important;
        }

        /* .action{
            height: 360px !important;
        } */

    }
    @media (max-width: 570px) { /* small devices (landscape phones, less than 768px) */

        #cadre > *{
            margin: 0rem !important;
        }
    }

</style>
@yield('style')
</head>
<body class="tm-container-1230px has-side-panel side-panel-right">
    <div class="side-panel-body-overlay"></div>
    <div id="side-panel-container" class="dark" data-tm-bg-img="images/side-push-bg.jpg">
      <div class="side-panel-wrap">
        <div id="side-panel-trigger-close" class="side-panel-trigger"><a href="#"><i class="fa fa-times side-panel-trigger-icon"></i></a></div>
        <img class="logo mb-50" src="{{ URL::asset('assets/front/images/logo1.png') }}" alt="Logo">
        <p>Lorem ipsum is simply free text dolor sit am adipi we help you ensure everyone is in the right jobs sicing elit, sed do consulting firms Et leggings across the nation tempor.</p>
        <div class="widget">
          <h4 class="widget-title widget-title-line-bottom line-bottom-theme-colored1">Latest News</h4>
          <div class="latest-posts">
            <article class="post clearfix pb-0 mb-10">
              <a class="post-thumb" href="news-details.html"><img src="images/blog/s1.jpg" alt="images"></a>
              <div class="post-right">
                <h5 class="post-title mt--0"><a href="news-details.html">Sustainable Construction</a></h5>
                <p>Lorem ipsum dolor...</p>
              </div>
            </article>
            <article class="post clearfix pb-0 mb-10">
              <a class="post-thumb" href="news-details.html"><img src="images/blog/s2.jpg" alt="images"></a>
              <div class="post-right">
                <h5 class="post-title mt--0"><a href="news-details.html">Industrial Coatings</a></h5>
                <p>Lorem ipsum dolor...</p>
              </div>
            </article>
            <article class="post clearfix pb-0 mb-10">
              <a class="post-thumb" href="news-details.html"><img src="images/blog/s3.jpg" alt="images"></a>
              <div class="post-right">
                <h5 class="post-title mt--0"><a href="news-details.html">Storefront Installations</a></h5>
                <p>Lorem ipsum dolor...</p>
              </div>
            </article>
          </div>
        </div>
  
        <div class="widget">
          <h5 class="widget-title widget-title-line-bottom line-bottom-theme-colored1">Contact Info</h5>
          <div class="tm-widget-contact-info contact-info-style1 contact-icon-theme-colored1">
            <ul>
              <li class="contact-name">
                <div class="icon"><i class="flaticon-contact-037-address"></i></div>
                <div class="text">John Doe</div>
              </li>
              <li class="contact-phone">
                <div class="icon"><i class="flaticon-contact-042-phone-1"></i></div>
                <div class="text"><a href="javascript:void(0)">+2250757088382</a></div>
              </li>
              <li class="contact-email">
                <div class="icon"><i class="flaticon-contact-043-email-1"></i></div>
                <div class="text"><a href="javascript:void(0)">info@nirvana.ci</a></div>
              </li>
              <li class="contact-address">
                <div class="icon"><i class="flaticon-contact-047-location"></i></div>
                <div class="text">Côte d'Ivoire, Abidjan Plateau, Bibliothèque du District</div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div id="wrapper" class="clearfix">
        @yield('preloader')
        @section('menu')
            @include('public.partials.menu')
        @show
        
        <div class="main-content-area">

            @yield('content')
        </div>
        @section('footer')
            @include('public.partials.footer')
        @show
    </div>

    <script src="{{ URL::asset('assets/front/js/custom.js') }}"></script>
    <script>
        $.ajaxSetup({

            headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),


            }

        });

        $(document).ready(function () {

            $("#newsletter").click(function () {

                var email =  $("#email").val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('newsletter') }}",
                    data: {'email': email},
                    success: function (response) {
                        $(".newsletter-response").remove();
                        if(response.success == true){
                            $(".subscribe-form").before("<p class='newsletter-response' style='color: #0f5132;'>"+response.message+"</p>");
                        }else{
                            $(".subscribe-form").before("<p class='newsletter-response' style='color: #842029;'>"+response.message+"</p>");
                        }
                    },
                    error: function (xhr) {
                        $(".newsletter-response").remove();
                        $(".subscribe-form").before("<p class='newsletter-response' style='color: #842029;'>Une erreur est survenue, Veuillez réessayer !</p>");
                    }
                });

                event.preventDefault();

            });

        });
    </script>
    @yield('scripts')
</body>
</html>
