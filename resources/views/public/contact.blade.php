@extends('public.layouts_index')
@section('content')
    <section class="page-title divider layer-overlay overlay-dark-5 section-typo-light bg-img-center" data-tm-bg-img="{{ URL::asset('assets/front/images/ban.png') }}">
        <div class="container pt-90 pb-90">
        <!-- Section Content -->
        <div class="section-content">
            <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="title text-white">Contact</h2>
                {{ Breadcrumbs::render('contact') }} 
            </div>
            </div>
        </div>
        </div>
    </section>
    <!-- Section: inner-header End -->
    <section class="mb-5">
        <div class="container pb-0 pb-lg-90">
            <div class="section-content">
                <div class="row align-items-center">
                    <div class="mb-md-30 col-sm-12 col-lg-4 col-md-6">
                        <div class="icon-box icon-left iconbox-theme-colored2 animate-icon-on-hover animate-icon-rotate mb-25">
                            <div class="icon-box-wrapper">
                            <div class="icon-wrapper">
                                <a class="icon icon-dark icon-type-font-icon"> <i class="flaticon-contact-045-call"></i> </a>
                            </div>
                            <div class="icon-text">
                                <h5 class="icon-box-title mt-0">Téléphone</h5>
                                <div class="content"><a href="javascript:void(0)">+2250757088382</a></div>
                            </div>
                            <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="icon-box icon-left iconbox-theme-colored2 animate-icon-on-hover animate-icon-rotate mb-25">
                            <div class="icon-box-wrapper">
                            <div class="icon-wrapper">
                                <a class="icon icon-dark icon-type-font-icon"> <i class="flaticon-contact-043-email-1"></i> </a>
                            </div>
                            <div class="icon-text">
                                <h5 class="icon-box-title mt-0">Email</h5>
                                <div class="content"><a href="javascript:void(0)">info@nirvana.ci</a></div>
                            </div>
                            <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="icon-box icon-left iconbox-theme-colored2 animate-icon-on-hover animate-icon-rotate mb-40">
                            <div class="icon-box-wrapper">
                            <div class="icon-wrapper">
                                <a class="icon icon-dark icon-type-font-icon"> <i class="flaticon-contact-025-world"></i> </a>
                            </div>
                            <div class="icon-text">
                                <h5 class="icon-box-title mt-0">Localisation</h5>
                                <div class="content">Abidjan Plateau, Bibliothèque du District</div>
                            </div>
                            <div class="clearfix"></div>
                            </div>
                        </div>
                        <ul class="styled-icons icon-dark icon-sm icon-circled mt-20">
                            <li><a href="https://www.linkedin.com/company/jci-nirvana/" target="_blank" data-tm-bg-color="#02B0E8" style="background-color: rgb(0, 119, 181) !important;"><i class="fab fa-linkedin"></i></a></li>
                            <li><a href="https://www.facebook.com/jcinirvana" target="_blank" data-tm-bg-color="#3B5998" style="background-color: rgb(59, 89, 152) !important;"><i class="fab fa-facebook"></i></a></li>
                            <li><a href="https://www.instagram.com/jcinirvana" target="_blank" data-tm-bg-color="#C13584" style="background-color: rgb(193, 53, 132) !important;"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="https://www.tiktok.com/@jcinirvana" target="_blank" data-tm-bg-color="#ff0050" style="background-color: rgb(255, 0, 80) !important;"><i class="fab fa-tiktok"></i></a></li>
                        </ul>
                    </div>
                    <div class="mb-md-30 col-sm-12 col-lg-8 col-md-6">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3972.6384673322586!2d-4.020155924185943!3d5.318960335986096!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfc1ebb3e5fdb853%3A0x64ed225d9447727a!2sBiblioth%C3%A8que%20du%20district%20d&#39;Abidjan!5e0!3m2!1sen!2sci!4v1717076851292!5m2!1sen!2sci" width="900" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
        
@endsection
