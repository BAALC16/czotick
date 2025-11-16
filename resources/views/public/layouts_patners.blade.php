<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>JCI Côte d'Ivoire</title>
<!-- Stylesheets -->
<link href="/front/css/bootstrap.css" rel="stylesheet">
<link href="/front/css/revolution-slider.css" rel="stylesheet">
<link href="/front/css/style.css" rel="stylesheet">
<!--Favicon-->
<link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
<link rel="icon" href="images/favicon.png" type="image/x-icon">
<!-- Responsive -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link href="/front/css/responsive.css" rel="stylesheet">
<style>
    .main-header-info .fa-facebook:hover, .main-header-info .fa-linkedin:hover{
        background: none !important;
        color: #0097dc !important;

    }

    .main-header-info .fa-facebook, .main-header-info .fa-linkedin
    {
        width: 15px !important;
    }

    .contact-section .default-form input[type="text"], .contact-section .default-form input[type="email"], .contact-section .default-form input[type="password"], .contact-section .default-form input[type="number"], .contact-section .default-form select, .contact-section .default-form textarea {
        color: #000;
    }

    .contact-section .default-form input[type="text"]:focus, .contact-section .default-form textarea:focus{
        color: #000;
    }
</style>
@yield('style')
</head>
<body>
<div class="page-wrapper">

    @section('menu')
        @include('public.partials.menu')
    @show

    @yield('content')

    <!--sponsors-subscribe Style-->
    <section class="sponsors-subscribe">
        <div class="container">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6 col-sm-12 col-xs-12 right-side" style="text-align: center;">
                    <div class="section-text">
                        <h5>Retrouvez nous sur:</h5>
                    </div>
                    <div class="icon-links">
                        <a href="https://web.facebook.com/jcicotedivoire225/"><i class="fa fa-facebook" aria-hidden="true" style="border: none;"></i></a>
                        <a href="https://www.linkedin.com/company/jci-c%C3%B4te-d-ivoire/"><i class="fa fa-linkedin" aria-hidden="true" style="border: none;"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--End sponsors-subscribe Style-->

    <!--Main Footer-->
    <footer class="main-footer">
        <div class="container">
            <!--Widgets Section-->
            <div class="widgets-section">
                <div class="row">
                    <!--Footer Column-->
                    <div class="footer-column col-md-3 col-sm-6 col-xs-12">
                        <div class="about-widget">
                            <div class="footer-logo">
                                <a href="index.html"><img src="images/logo.png" alt=""></a>
                            </div>
                            <div class="widget-content">
                                <p>La JCI est une organisation mondiale de jeunes ( 18-40 ans) citoyens actifs au service de leur communauté dans le but d'apporter un changement positif.</p>
                            </div>
                        </div>
                    </div>
                    <!--Footer Column-->
                    <div class="footer-column col-md-3 col-sm-6 col-xs-12">
                        <div class="links-widget">
                            <h4>liens rapides</h4>
                            <ul class="links-list">
                                <li><a href="/presentation"><i class="fa fa-angle-right" aria-hidden="true"></i>Présentation</a></li>
                                <li><a href="/blog"><i class="fa fa-angle-right" aria-hidden="true"></i>Blog</a></li>
                                <li><a href="/activites"><i class="fa fa-angle-right" aria-hidden="true"></i>Activités</a></li>
                                <li><a href="/partenaires"><i class="fa fa-angle-right" aria-hidden="true"></i>Partenaires</a></li>
                            </ul>
                        </div>
                    </div>
                    <!--Footer Column-->
                    <div class="footer-column col-md-3 col-sm-6 col-xs-12">
                        <div class="contact-links">
                            <h4>Contactez-nous</h4>
                            <div class="widget-content">
                                <div class="footer-info">
                                    <i class="fa fa-home" aria-hidden="true"></i>
                                    <h6>Avenue Lamblin, Plateau, Abidjan, 225 Abidjan, Côte d’ivoire</h6>
                                </div>
                                <div class="footer-info">
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                    <h6>+225 0747931391</h6>
                                </div>
                                <div class="footer-info">
                                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                    <a href="#"><h6>contact@jci.ci</h6></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Footer Column-->
                    <div class="footer-column col-md-3 col-sm-6 col-xs-12">
                        <h4>galérie</h4>
                        <div class="gallery-widget">
                            <div class="row">
                                @forelse ($galleriesFooter as $galleryFooter)
                                    <div class="image-column col-md-4 col-sm-4 col-xs-6">
                                        <figure>
                                            <a href="{{ route('public.gallery') }}"><img src="/public/storage/{{ $galleryFooter->image }}" alt="" style="width: 83px; height: 83px;"></a>
                                        </figure>
                                    </div>
                                @empty

                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Footer Bottom-->
        <div class="footer-bottom">
            <div class="container">
                <div class="copyright-text text-center">Copyright &copy; . Ayiyikoh Tous droits réservés</div>
            </div>
        </div>
    </footer>
    <!--Main Footer-->

</div>
<!--End pagewrapper-->

<!--Scroll to top-->
<div class="scroll-to-top scroll-to-target" data-target=".main-header"><span class="icon fa fa-long-arrow-up"></span></div>


<script src="/front/js/jquery.js"></script>
<script type="text/javascript" src="/front/js/jquery-2.1.4.js"></script>
<script src="/front/js/jquery.bxslider.min.js"></script>
<script src="/front/js/bootstrap.min.js"></script>
<script src="/front/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="/front/js/jquery-ui.min.js"></script>
<script src="/front/js/revolution.min.js"></script>
<script src="/front/js/isotope.js"></script>
<script src="/front/js/jquery.fancybox.pack.js"></script>
<script src="/front/js/jquery.fancybox-media.js"></script>
<script src="/front/js/html5lightbox.js"></script>
<script src="/front/js/circle-progress.js"></script>
<script src="/front/js/owl.js"></script>
<script type="text/javascript" src="/front/js/jquery.mixitup.min.js"></script>
<script src="/front/js/masterslider/masterslider.js"></script>
<script src="/front/js/owl.carousel.min.js"></script>
<script src="/front/js/mixitup.js"></script>
<script src="/front/js/validate.js"></script>
<script src="/front/js/wow.js"></script>
<script src="/front/js/jquery.appear.js"></script>
<script src="/front/js/jquery.countTo.js"></script>
<script src="/front/js/script.js"></script>
@yield('scripts')
</body>
</html>
