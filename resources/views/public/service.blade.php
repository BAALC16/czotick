@extends("public.layouts")
@section('title') {{ $service->label }} @endsection
@section('specific-css')
    <link href="{{ asset('assets/intlinput/css/intlTelInput.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('vendors/stylesearchbar.css') }}" type="text/css" rel="stylesheet">
    <style>
        .iti{
            width: 79%;
        }
    </style>
@endsection
@section('content')
    <div class="bg-gray-01">
        <section class="pb-6 pt-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('public.services.index') }}">Services</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $service->label }}</li>
                    </ol>
                </nav>
            </div>
        </section>
        <section class="pb-11">
            <div class="container">
                <div class="row h-100">
                    <div class="col-lg-8">
                        <div class="card border-0 px-6 pt-6 pb-10">
                            <div class="row h-100">
                                <div class="col-sm-5 mb-6 mb-sm-0">
                                    <img class="rounded-lg border card-img" src="{{ $service->image_url }}"
                                        alt="{{ $service->label }}">
                                </div>
                                <div class="col-sm-7">
                                    <div class="card-body p-0">
                                        <h2 class="card-title fs-22 lh-15 mb-1 text-dark">
                                            {{ $service->label }}
                                        </h2>
                                        <p class="card-text mb-1">
                                            Service actif
                                        </p>
                                        <ul class="list-inline mb-3">
                                            <li class="list-inline-item fs-13 text-heading font-weight-500">4.8/5</li>
                                            <li class="list-inline-item fs-13 text-heading font-weight-500 mr-1">
                                                <ul class="list-inline mb-0">
                                                    <li class="list-inline-item mr-0">
                                                        <span class="text-warning fs-12 lh-2"><i
                                                                class="fas fa-star"></i></span>
                                                    </li>
                                                    <li class="list-inline-item mr-0">
                                                        <span class="text-warning fs-12 lh-2"><i
                                                                class="fas fa-star"></i></span>
                                                    </li>
                                                    <li class="list-inline-item mr-0">
                                                        <span class="text-warning fs-12 lh-2"><i
                                                                class="fas fa-star"></i></span>
                                                    </li>
                                                    <li class="list-inline-item mr-0">
                                                        <span class="text-warning fs-12 lh-2"><i
                                                                class="fas fa-star"></i></span>
                                                    </li>
                                                    <li class="list-inline-item mr-0">
                                                        <span class="text-warning fs-12 lh-2"><i
                                                                class="fas fa-star"></i></span>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="list-inline-item fs-13 text-gray-light">(67 reviews)</li>
                                        </ul>
                                        <h3 class="card-title fs-22 lh-15 mb-1 text-dark">
                                            Facturation
                                        </h3>
                                        <ul class="list-group list-group-no-border mb-4">
                                            <li
                                                class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                @if ($service->prix)
                                                    <span class="col-sm-3 px-1">&Agrave; partir de </span>
                                                    <span
                                                        class="col-sm-9 px-1 text-heading font-weight-500">{{ $service->prix }}
                                                        FCFA</span>
                                                @else
                                                    <a href="{{ url('/contact') }}"
                                                        class="col-sm-9 px-1 font-weight-500">Contactez-nous</a>
                                                @endif
                                            </li>
                                        </ul>
                                        <h3 class="card-title fs-22 lh-15 mb-1 text-dark">
                                            Disponibilité
                                        </h3>
                                        <ul class="list-group list-group-no-border">
                                            <li
                                                class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                <span class="col-sm-4 px-1">Lundi</span>
                                                <span class="col-sm-8 px-1 text-heading font-weight-500">8:00 - 17:00
                                                    UTC</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                <span class="col-sm-4 px-1">Mardi</span>
                                                <span class="col-sm-8 px-1 text-heading font-weight-500">8:00 - 17:00
                                                    UTC</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                <span class="col-sm-4 px-1">Mercredi</span>
                                                <span class="col-sm-8 px-1 text-heading font-weight-500">8:00 - 17:00
                                                    UTC</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                <span class="col-sm-4 px-1">Jeudi</span>
                                                <span class="col-sm-8 px-1 text-heading font-weight-500">8:00 - 17:00
                                                    UTC</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                <span class="col-sm-4 px-1">Vendredi</span>
                                                <span class="col-sm-8 px-1 text-heading font-weight-500">8:00 - 17:00
                                                    UTC</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                <span class="col-sm-4 px-1">Samedi</span>
                                                <span class="col-sm-8 px-1 text-heading font-weight-500">9:00 - 15:00
                                                    UTC</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                <span class="col-sm-4 px-1">Dimanche</span>
                                                <span class="col-sm-8 px-1 text-heading font-weight-500">9:00 - 12:00
                                                    UTC</span>
                                            </li>
                                            <li
                                                class="list-group-item d-flex align-items-sm-center lh-214 row mb-0 mt-3 mx-n1 p-0 ">
                                                <span class="col-sm-3 px-1">Social</span>
                                                <ul class="col-sm-9 list-inline text-gray-lighter m-0 px-1">
                                                    <li class="list-inline-item m-0">
                                                        <a href="#"
                                                            class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                                class="fab fa-twitter"></i></a>
                                                    </li>
                                                    <li class="list-inline-item mr-0 ml-2">
                                                        <a href="#"
                                                            class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                                class="fab fa-facebook-f"></i></a>
                                                    </li>
                                                    <li class="list-inline-item mr-0 ml-2">
                                                        <a href="#"
                                                            class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                                class="fab fa-instagram"></i></a>
                                                    </li>
                                                    <li class="list-inline-item mr-0 ml-2">
                                                        <a href="#"
                                                            class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                                class="fab fa-linkedin-in"></i></a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 blockui">
                            <div class="card-body p-6">
                                <h3 class="card-title fs-16 text-dark mb-6">
                                    Demander ce service
                                </h3>
                                <form method="POST" class="form-xhr"
                                    action="{{ route('public.services.book', $service) }}">
                                    @csrf
                                    <div class="form-group mb-2">
                                        <input type="text" class="form-control form-control-lg border-0" placeholder="Nom"
                                            name="nom" @auth value="{{ auth()->user()->nom }}" readonly @endauth required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="text" class="form-control form-control-lg border-0"
                                            placeholder="Prénoms" name="prenoms" @auth
                                            value="{{ auth()->user()->prenoms }}" readonly @endauth required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="email" class="form-control form-control-lg border-0"
                                            placeholder="E-mail" name="email" @auth value="{{ auth()->user()->email }}"
                                            readonly @endauth required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="tel" class="form-control form-control-lg border-0"
                                            placeholder="Téléphone" @auth value="{{ auth()->user()->mobile }}" readonly
                                            @endauth name="ph" id="phone">
                                        <input type="hidden" name="phone_number[mobile]">
                                    </div>
                                    <div class="form-group mb-4">
                                        <textarea class="form-control form-control-lg border-0" rows="5"
                                            placeholder="Message" name="note"></textarea>
                                    </div>
                                    @auth
                                        <input type="hidden" name="user_id" value="{{ auth()->id() }}" />
                                    @endauth
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Continuer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Modal add new Role --}}
            <div class="modal fade" id="modal_create_demande" data-backdrop="static" data-keyboard="false" tabindex="-1"
                role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Détails de la demande</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body blockui">
                            <form method="post" id="form_create_demande" class="form-xhr"
                                action="{{ route('public.demandes.submit', ['reservation' => '__id__']) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="_method" value="PUT" />
                                <div class="row">
                                    <div class="col-lg-2 col-xl-3"></div>
                                    <div class="col-lg-8 col-xl-6">
                                        <div id="attributs-group"></div>
                                        <div class="mb-3">
                                            <label class="form-label h6">Note</label>
                                            <textarea class="form-control form-control-lg border-0 form-control-textarea"
                                                name="note" rows="5"></textarea>
                                        </div>
                                        <div class="text-center py-3">
                                            <button type="submit" class="btn btn-lg btn-primary">Terminer</button>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-xl-3"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


        </section>
        <section class="pb-11">
            <div class="container">
                <div class="collapse-tabs mb-10">
                    <ul class="nav nav-tabs text-uppercase d-none d-md-inline-flex agent-details-tabs" role="tablist">
                        <li class="nav-item">
                            <a href="#overview" class="nav-link active shadow-none fs-13" data-toggle="tab" role="tab">
                                Vue d'ensemble
                            </a>
                        </li>
                        <li class="nav-item ml-0">
                            <a href="#reviews" class="nav-link shadow-none fs-13" data-toggle="tab" role="tab">
                                Evaluations
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content shadow-none py-7 px-6 bg-white">
                        <div id="collapse-tabs-accordion-01">
                            <div class="tab-pane tab-pane-parent fade show active" id="overview" role="tabpanel">
                                <div class="card border-0 bg-transparent">
                                    <div class="card-header border-0 d-block d-md-none bg-transparent px-0 py-1">
                                        <h5 class="mb-0">
                                            <button
                                                class="btn lh-2 fs-18 bg-white py-1 px-6 mb-4 shadow-none w-100 collapse-parent border"
                                                data-toggle="collapse" data-target="#overview-collapse" aria-expanded="true"
                                                aria-controls="overview-collapse">
                                                Vue d'ensemble
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="overview-collapse" class="collapse show collapsible"
                                        data-parent="#collapse-tabs-accordion-01">
                                        <div class="card-body p-0">
                                            <h2 class="card-title fs-22 lh-15 mb-1 text-dark">
                                                Description
                                            </h2>
                                            {!! $service->description !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane tab-pane-parent fade" id="reviews" role="tabpanel">
                                <div class="card border-0 bg-transparent">
                                    <div class="card-header border-0 d-block d-md-none bg-transparent p-0">
                                        <h5 class="mb-0">
                                            <button
                                                class="btn lh-2 fs-18 bg-white py-1 px-6 shadow-none w-100 collapse-parent border collapsed mb-4"
                                                data-toggle="collapse" data-target="#reviews-collapse" aria-expanded="true"
                                                aria-controls="reviews-collapse">
                                                Evaluations
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="reviews-collapse" class="collapse collapsible pb-6 pb-md-0"
                                        data-parent="#collapse-tabs-accordion-01">
                                        <div class="card-body p-0">
                                            <h2 class="fs-22 text-heading lh-15 mb-6">Agency Rating & Reviews </h2>
                                            <div class="row mb-6">
                                                <div class="col-sm-6 mb-6 mb-sm-0">
                                                    <h5 class="fs-16 lh-2 text-heading mb-6">
                                                        Avarage User Rating
                                                    </h5>
                                                    <p class="fs-40 text-heading font-weight-bold mb-6 lh-1">4.6 <span
                                                            class="fs-18 text-gray-light font-weight-normal">/5</span></p>
                                                    <ul class="list-inline">
                                                        <li
                                                            class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li
                                                            class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li
                                                            class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li
                                                            class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li
                                                            class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-sm-6">
                                                    <h5 class="fs-16 lh-2 text-heading mb-5">
                                                        Rating Breakdown
                                                    </h5>
                                                    <div class="d-flex align-items-center mx-n1">
                                                        <ul class="list-inline d-flex px-1 mb-0">
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                        </ul>
                                                        <div class="d-block w-100 px-1">
                                                            <div class="progress rating-progress">
                                                                <div class="progress-bar bg-warning" role="progressbar"
                                                                    style="width: 60%" aria-valuenow="60" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="text-muted px-1">60%</div>
                                                    </div>
                                                    <div class="d-flex align-items-center mx-n1">
                                                        <ul class="list-inline d-flex px-1 mb-0">
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                        </ul>
                                                        <div class="d-block w-100 px-1">
                                                            <div class="progress rating-progress">
                                                                <div class="progress-bar bg-warning" role="progressbar"
                                                                    style="width: 40%" aria-valuenow="40" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="text-muted px-1">40%</div>
                                                    </div>
                                                    <div class="d-flex align-items-center mx-n1">
                                                        <ul class="list-inline d-flex px-1 mb-0">
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                        </ul>
                                                        <div class="d-block w-100 px-1">
                                                            <div class="progress rating-progress">
                                                                <div class="progress-bar bg-warning" role="progressbar"
                                                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-muted px-1">0%</div>
                                                    </div>
                                                    <div class="d-flex align-items-center mx-n1">
                                                        <ul class="list-inline d-flex px-1 mb-0">
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                        </ul>
                                                        <div class="d-block w-100 px-1">
                                                            <div class="progress rating-progress">
                                                                <div class="progress-bar bg-warning" role="progressbar"
                                                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-muted px-1">0%</div>
                                                    </div>
                                                    <div class="d-flex align-items-center mx-n1">
                                                        <ul class="list-inline d-flex px-1 mb-0">
                                                            <li class="list-inline-item text-warning mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                            <li class="list-inline-item text-border mr-1">
                                                                <i class="fas fa-star"></i>
                                                            </li>
                                                        </ul>
                                                        <div class="d-block w-100 px-1">
                                                            <div class="progress rating-progress">
                                                                <div class="progress-bar bg-warning" role="progressbar"
                                                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-muted px-1">0%</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h3 class="fs-16 lh-2 text-heading mb-6">67 Reviews</h3>
                                            <div class="row border-bottom mb-6 pb-6 mb-6">
                                                <div class="col-md-3 mb-4 mb-md-0">
                                                    <div class="media">
                                                        <div class="w-70px h-70 mr-2">
                                                            <img src="images/review-06.jpg" alt="Dollie Horton">
                                                        </div>
                                                        <div class="media-body">
                                                            <p class="fs-16 font-weight-500 text-heading mb-0 lh-15">
                                                                Dollie Horton</p>
                                                            <p class=" mb-0">San Diego</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="d-flex mb-1">
                                                        <ul class="list-inline mb-2 mb-lg-0">
                                                            <li
                                                                class="list-inline-item fs-13 text-heading font-weight-500 mr-1">
                                                                <ul class="list-inline mb-0">
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                        <p class="ml-auto mb-0 text-muted">
                                                            02 Dec 2016 at 2:40pm
                                                        </p>
                                                    </div>
                                                    <p class="mb-0">Very good and fast support during the week.
                                                        Thanks for always keeping your WordPress themes up to date. Your
                                                        level of support and dedication is second to none. Solved all my
                                                        problems in a pressing time! Excited to see the other themes they
                                                        make!</p>
                                                </div>
                                            </div>
                                            <div class="row border-bottom mb-6 pb-6 mb-6">
                                                <div class="col-md-3 mb-4 mb-md-0">
                                                    <div class="media">
                                                        <div class="w-70px h-70 mr-2">
                                                            <img src="images/review-01.jpg" alt="Dollie Horton">
                                                        </div>
                                                        <div class="media-body">
                                                            <p class="fs-16 font-weight-500 text-heading mb-0 lh-15">
                                                                Dollie Horton</p>
                                                            <p class=" mb-0">San Diego</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="d-flex mb-1">
                                                        <ul class="list-inline mb-2 mb-lg-0">
                                                            <li
                                                                class="list-inline-item fs-13 text-heading font-weight-500 mr-1">
                                                                <ul class="list-inline mb-0">
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                        <p class="ml-auto mb-0 text-muted">
                                                            02 Dec 2016 at 2:40pm
                                                        </p>
                                                    </div>
                                                    <p class="mb-0">Very good and fast support during the week.
                                                        Thanks for always keeping your WordPress themes up to date. Your
                                                        level of support and dedication is second to none. Solved all my
                                                        problems in a pressing time! Excited to see the other themes they
                                                        make!</p>
                                                </div>
                                            </div>
                                            <div class="row border-bottom mb-6 pb-6 mb-6">
                                                <div class="col-md-3 mb-4 mb-md-0">
                                                    <div class="media">
                                                        <div
                                                            class="w-70px h-70 mr-2 bg-gray-01 rounded-circle fs-18 text-muted d-flex align-items-center justify-content-center">
                                                            DH
                                                        </div>
                                                        <div class="media-body">
                                                            <p class="fs-16 font-weight-500 text-heading mb-0 lh-15">
                                                                Dollie Horton</p>
                                                            <p class=" mb-0">San Diego</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="d-flex mb-1">
                                                        <ul class="list-inline mb-2 mb-lg-0">
                                                            <li
                                                                class="list-inline-item fs-13 text-heading font-weight-500 mr-1">
                                                                <ul class="list-inline mb-0">
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                        <p class="ml-auto mb-0 text-muted">
                                                            02 Dec 2016 at 2:40pm
                                                        </p>
                                                    </div>
                                                    <p class="mb-0">Very good and fast support during the week.
                                                        Thanks for always keeping your WordPress themes up to date. Your
                                                        level of support and dedication is second to none. Solved all my
                                                        problems in a pressing time! Excited to see the other themes they
                                                        make!</p>
                                                </div>
                                            </div>
                                            <div class="row  pb-6 mb-6">
                                                <div class="col-md-3 mb-4 mb-md-0">
                                                    <div class="media">
                                                        <div class="w-70px h-70 mr-2">
                                                            <img src="images/review-02.jpg" alt="Dollie Horton">
                                                        </div>
                                                        <div class="media-body">
                                                            <p class="fs-16 font-weight-500 text-heading mb-0 lh-15">
                                                                Dollie Horton</p>
                                                            <p class=" mb-0">San Diego</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="d-flex mb-1">
                                                        <ul class="list-inline mb-2 mb-lg-0">
                                                            <li
                                                                class="list-inline-item fs-13 text-heading font-weight-500 mr-1">
                                                                <ul class="list-inline mb-0">
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                    <li class="list-inline-item mr-0">
                                                                        <span class="text-warning fs-12 lh-2"><i
                                                                                class="fas fa-star"></i></span>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                        <p class="ml-auto mb-0 text-muted">
                                                            02 Dec 2016 at 2:40pm
                                                        </p>
                                                    </div>
                                                    <p class="mb-0">Very good and fast support during the week.
                                                        Thanks for always keeping your WordPress themes up to date. Your
                                                        level of support and dedication is second to none. Solved all my
                                                        problems in a pressing time! Excited to see the other themes they
                                                        make!</p>
                                                </div>
                                            </div>
                                            <a href="#"
                                                class="btn btn-lg text-heading bg-hover-light border fs-14 px-5 mb-6">View
                                                more <span class="text-primary fs-15 text-primary d-inline-block ml-2">
                                                    <i class="fal fa-long-arrow-down"></i>
                                                </span> </a>
                                            <h3 class="fs-16 lh-2 text-heading mb-4">Write A Review</h3>
                                            <form>
                                                <div class="form-group mb-4 d-flex justify-content-start">
                                                    <div class="rate-input">
                                                        <input type="radio" id="star5" name="rate" value="5" />
                                                        <label for="star5" title="text" class="mb-0 mr-1 lh-1">
                                                            <i class="fas fa-star"></i>
                                                        </label>
                                                        <input type="radio" id="star4" name="rate" value="4" />
                                                        <label for="star4" title="text" class="mb-0 mr-1 lh-1">
                                                            <i class="fas fa-star"></i>
                                                        </label>
                                                        <input type="radio" id="star3" name="rate" value="3" />
                                                        <label for="star3" title="text" class="mb-0 mr-1 lh-1">
                                                            <i class="fas fa-star"></i>
                                                        </label>
                                                        <input type="radio" id="star2" name="rate" value="2" />
                                                        <label for="star2" title="text" class="mb-0 mr-1 lh-1">
                                                            <i class="fas fa-star"></i>
                                                        </label>
                                                        <input type="radio" id="star1" name="rate" value="1" />
                                                        <label for="star1" title="text" class="mb-0 mr-1 lh-1">
                                                            <i class="fas fa-star"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group mb-4">
                                                            <input placeholder="Your Name"
                                                                class="form-control form-control-lg border-0" type="text"
                                                                name="name">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group mb-4">
                                                            <input type="email" placeholder="Email" name="email"
                                                                class="form-control form-control-lg border-0">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-6">
                                                    <textarea class="form-control border-0" placeholder="Your Review"
                                                        name="message" rows="5"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-lg btn-primary px-9">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('specific-js')


    <script src="{{ asset('assets/intlinput/js/intlTelInput.js') }}"></script>
    <script>
        var input = document.querySelector("#phone");
        var iti= window.intlTelInput(input, {
            // allowDropdown: false,
            // autoHideDialCode: false,
            // autoPlaceholder: "off",
            // dropdownContainer: document.body,
            // excludeCountries: ["us"],
            // formatOnDisplay: false,
            // geoIpLookup: function(callback) {
            //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
            //     var countryCode = (resp && resp.country) ? resp.country : "";
            //     callback(countryCode);
            //   });
            // },
            hiddenInput: "mobile",
            initialCountry: "ci",
            // localizedCountries: { 'de': 'Deutschland' },
            nationalMode: true,
            // separateDialCode:true,
            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
            // placeholderNumberType: "MOBILE",
            // preferredCountries: ['cn', 'jp'],
            // separateDialCode: true,
            utilsScript: "{{asset('assets/intlinput/js/utils.js')}}",
        });
        var handleChange = function() {

        };

        // listen to "keyup", but also "change" to update when the user selects a country
        input.addEventListener('change', handleChange);
        input.addEventListener('keyup', handleChange);
    </script>
    <script src="/vendors/formrepeater/formrepeater.js"></script>
    <script>
        $(document).on('demande_initiated', function (e) {
            let demande = e.parameters.demande,
            service = e.parameters.service,
            attributs = e.parameters.attributs,
            grouper = $('#attributs-group');
            $('#form_create_demande').attr('action', $('#form_create_demande').attr('action').replace(/__id__/g, demande.id))
            $('#form_create_demande [name=note]').text(demande.note)
            const modal = new bootstrap.Modal(document.getElementById('modal_create_demande'));
            for (let i = 0; i < attributs.length; i++) {
                let attr = attributs[i];
                if(attr.type_champ == 'text') {
                    grouper.append(`
                        <div class="mb-6">
                            <label class="form-label h6">
                                <span>${attr.label}</span>
                                `+((attr.description !== null && attr.description.length > 0) ? `<i class="fas fa-exclamation-circle ml-1 fs-2 text-muted" data-toggle="tooltip" title="${attr.description}"></i>` : ``)+`
                            </label>
                            <input type="text" placeholder="${attr.label}" class="form-control form-control-lg border-0" name="attributs[${attr.id}]" />
                        </div>
                    `);
                } else if(attr.type_champ == 'textarea') {
                    grouper.append(`
                        <div class="mb-6">
                            <label class="form-label h6">
                                <span>${attr.label}</span>
                                `+((attr.description !== null && attr.description.length > 0) ? `<i class="fas fa-exclamation-circle ml-1 fs-2 text-muted" data-toggle="tooltip" title="${attr.description}"></i>` : ``)+`
                            </label>
                            <textarea class="form-control form-control-textarea form-control-lg border-0" name="attributs[${attr.id}]" placeholder="${attr.label}"></textarea>
                        </div>
                    `);
                } else if(attr.type_champ == 'file') {
                    grouper.append(`
                        <div class="mb-6">
                            <label class="form-label h6">
                                <span>${attr.label}</span>
                                `+((attr.description !== null && attr.description.length > 0) ? `<i class="fas fa-exclamation-circle ml-1 fs-2 text-muted" data-toggle="tooltip" title="${attr.description}"></i>` : ``)+`
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile${attr.id}" name="attributs[${attr.id}]">
                                <label class="custom-file-label" for="customFile${attr.id}" data-browse="Parcourir">Choisir un fichier</label>
                            </div>
                        </div>
                    `);
                } else if(attr.type_champ == 'files') {
                    // use form-repeater here
                    grouper.append(`
                        <div id="repeater-${attr.id}" class="mb-6">
                            <div data-repeater-list="attributs[${attr.id}]">
                                <div class="form-group">
                                    <div class="mb-3">
                                        <label class="form-label h6">
                                            <span>${attr.label}</span>
                                            `+((attr.description !== null && attr.description.length > 0) ? `<i class="fas fa-exclamation-circle ml-1 fs-2 text-muted" data-toggle="tooltip" title="${attr.description}"></i>` : ``)+`
                                        </label>
                                        <div data-repeater-item="attributs_item" class="mb-3">
                                            <div class="d-flex">
                                                <div class="flex-column flex-grow-1">
                                                    <div class="custom-file">
                                                        <input name="file" type="file" class="custom-file-input" id="customFile${attr.id}_${(Math.floor(Math.random() * 100) + 1)}">
                                                        <label class="custom-file-label" for="customFile${attr.id}_${(Math.floor(Math.random() * 100) + 1)}" data-browse="Parcourir">Choisir un fichier</label>
                                                    </div>
                                                </div>
                                                <div class="flex-column">
                                                    <a href="javascript:;" data-repeater-delete class="ml-3 btn btn-outline btn-outline-danger"><i class="fal fa-trash-alt"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <a href="javascript:;" data-repeater-create class="btn btn-outline btn-outline-primary">
                                    <i class="fal fa-plus"></i> Ajouter un fichier
                                </a>
                            </div>
                        </div>
                    `);

                    $('#repeater-'+attr.id).repeater({
                        initEmpty: false,
                        show: function() {
                            $(this).slideDown();
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip();
                        },

                        hide: function(deleteElement) {
                            $(this).slideUp(deleteElement);
                        }
                    });
                }
            }
            $('[data-toggle="tooltip"]').tooltip('dispose');
            $('[data-toggle="tooltip"]').tooltip();
            modal.show();
        })
    </script>
@endsection
