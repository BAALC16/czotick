@extends("public.layouts")
@section('title', $property->title . ' - ' . $property->fullAddress())
@section('specific-css')
    <link href="{{ asset('assets/rating/css/star-rating-svg.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/intlinput/css/intlTelInput.css') }}" type="text/css" rel="stylesheet">
    <style>
        .iti {
            width: 79%;
        }
    </style>
@endsection
@section('content')
    <div class="primary-content bg-gray-01 pb-12">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb py-3">
                    <li class="breadcrumb-item fs-12 letter-spacing-087">
                        <a href="/">Accueil</a>
                    </li>
                    <li class="breadcrumb-item fs-12 letter-spacing-087">
                        <a href="{{ route('public.listing', ['transaction' => $property->purpose, 'type' => $property->propertyType->slug]) }}">{{ ucwords($property->propertyType->slug) . ' en ' . ucwords($property->purpose)}}</a>
                    </li>
                    <li class="breadcrumb-item fs-12 letter-spacing-087 active">{{$property->title}}</li>
                </ol>
            </nav>
            <div class="d-md-flex justify-content-md-between mb-1">
                <ul class="list-inline d-sm-flex align-items-sm-center mb-0">
                    <li class="list-inline-item badge badge-indigo">{{$property->propertyType->name}}</li>
                    @if ($property->purpose == "location")
                        <li class="list-inline-item badge mr-3 badge-primary">{{ $property->purpose }}</li>
                    @else
                        <li class="list-inline-item badge mr-3 badge-orange">{{ $property->purpose }}</li>
                    @endif
                    <li class="list-inline-item mr-2 mt-2 mt-sm-0"><i
                            class="fal fa-clock mr-1"></i>{{ $property->shortDateCreated() }}</li>
                    <li class="list-inline-item mt-2 mt-sm-0"><i
                            class="fal fa-user mr-1"></i>{{$property->user->full_name}}</li>

                </ul>
                <ul class="list-inline mb-0 mr-n2 my-4 my-md-0">
                    <li class="list-inline-item mr-2">
                        <a href="#" class="btn btn-outline-light px-3 text-body d-flex align-items-center h-32 border">
                            <i class="far fa-heart mr-2 fs-15 text-primary"></i>Save
                        </a>
                    </li>
                </ul>
            </div>
            <div class="d-md-flex justify-content-md-between mb-6">
                <div>
                    <h2 class="fs-35 font-weight-600 lh-15 text-heading">{{$property->title}}</h2>
                    <p class="mb-0"><i
                            class="fal fa-map-marker-alt mr-2"></i>{{$property->fullAddress()}}@if ($property->layoutType)
                            - {{ $property->layoutType->name }}
                        @endif</p>
                </div>
                <div class="mt-2 text-md-right">
                    <p class="fs-22 text-heading font-weight-bold mb-0 mr-2">@money($property->price) FCFA
                        @if ($property->purpose == "location")
                            / mois
                        @endif
                    </p>
                </div>
            </div>
            <div class="row">
                <article class="col-lg-8">
                    <section>
                        <div class="galleries position-relative">
                            <div class="position-absolute pos-fixed-top-right z-index-3">
                                <ul class="list-inline pt-4 pr-5">
                                    <li class="list-inline-item mr-2">
                                        <a href="#" data-toggle="tooltip" title="Favourite"
                                           class="d-flex align-items-center justify-content-center w-40px h-40 bg-white text-heading bg-hover-primary hover-white rounded-circle">
                                            <i class="far fa-heart"></i></a>
                                    </li>
                                    <li class="list-inline-item mr-2">
                                        <button type="button"
                                                class="btn btn-white p-0 d-flex align-items-center justify-content-center w-40px h-40 text-heading bg-hover-primary hover-white rounded-circle border-0 shadow-none"
                                                data-container="body" data-toggle="popover" data-placement="top"
                                                data-html="true" data-content=' <ul class="list-inline mb-0">
                      <li class="list-inline-item">
                        <a href="#" class="text-muted fs-15 hover-dark lh-1 px-2"><i class="fab fa-twitter"></i></a>
                      </li>
                      <li class="list-inline-item ">
                        <a href="#" class="text-muted fs-15 hover-dark lh-1 px-2"><i class="fab fa-facebook-f"></i></a>
                      </li>
                      <li class="list-inline-item">
                        <a href="#" class="text-muted fs-15 hover-dark lh-1 px-2"><i class="fab fa-instagram"></i></a>
                      </li>
                      <li class="list-inline-item">
                        <a href="#" class="text-muted fs-15 hover-dark lh-1 px-2"><i class="fab fa-youtube"></i></a>
                      </li>
                    </ul>
                    '>
                                            <i class="far fa-share-alt"></i>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="slick-slider slider-for-01 arrow-haft-inner mx-0"
                                 data-slick-options='{"slidesToShow": 1, "autoplay":false,"dots":false,"arrows":false,"asNavFor": ".slider-nav-01"}'>
                                @if (!($property->gallery->isEmpty()))
                                    @foreach($property->gallery as $gallery)
                                        @if (Storage::disk('public')->exists('property/gallery/'.$gallery->name))
                                            <div class="box px-0">
                                                <div class="item item-size-3-2">
                                                    <div class="card p-0 hover-change-image">
                                                        <a href="{{Storage::url('property/gallery/'.$gallery->name)}}"
                                                           class="card-img" data-gtf-mfp="true" data-gallery-id="04"
                                                           style="background-image:url('{{Storage::url('property/gallery/'.$gallery->name)}}')">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                @if (!empty($property->floor_plan) && Storage::disk('public')->exists('property/floor/'.$property->floor_plan))
                                    <div class="box px-0">
                                        <div class="item item-size-3-2">
                                            <div class="card p-0 hover-change-image">
                                                <a href="{{Storage::url('property/floor/'.$property->floor_plan)}}"
                                                   class="card-img" data-gtf-mfp="true" data-gallery-id="04"
                                                   style="background-image:url('{{Storage::url('property/floor/'.$property->floor_plan)}}')">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="slick-slider slider-nav-01 mt-4 mx-n1 arrow-haft-inner"
                                 data-slick-options='{"slidesToShow": 5, "autoplay":false,"dots":false,"arrows":false,"asNavFor": ".slider-for-01","focusOnSelect": true,"responsive":[{"breakpoint": 768,"settings": {"slidesToShow": 4}},{"breakpoint": 576,"settings": {"slidesToShow": 2}}]}'>
                                @if (!($property->gallery->isEmpty()))
                                    @foreach($property->gallery as $gallery)
                                        @if (Storage::disk('public')->exists('property/gallery/'.$gallery->name))
                                            <div class="box pb-6 px-0">
                                                <div class="bg-hover-white p-1 shadow-hover-xs-3 h-100 rounded-lg">
                                                    <img src="{{Storage::url('property/gallery/'.$gallery->name)}}"
                                                         alt="{{ $property->title}}" class="h-100 w-100 rounded-lg">
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                @if (!empty($property->floor_plan) && Storage::disk('public')->exists('property/floor/'.$property->floor_plan))
                                    <div class="box pb-6 px-0">
                                        <div class="bg-hover-white p-1 shadow-hover-xs-3 h-100 rounded-lg">
                                            <img src="{{Storage::url('property/floor/'.$property->floor_plan)}}"
                                                 alt="{{ $property->title}}" class="h-100 w-100 rounded-lg">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>
                    <section class="pb-8 px-6 pt-5 bg-white rounded-lg">
                        <h4 class="fs-22 text-heading mb-3">Description</h4>
                        <p class="mb-0 lh-214">{!! $property->description !!}</p>
                    </section>
                    @if (count($property->features()->get()))
                        <section class="mt-2 pb-3 px-6 pt-5 bg-white rounded-lg">
                            <h4 class="fs-22 text-heading mb-6">Options</h4>
                            <div class="row">
                                @foreach($property->features()->get() as $feature)
                                    <div class="col-lg-3 col-sm-4 mb-6">
                                        <div class="media">
                                            <div class="p-2 shadow-xxs-1 rounded-lg mr-2 lh-1">
                                                <h4 class="{{$feature->icon}}"></h4>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="fs-13 font-weight-normal mt-2">{{$feature->name}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                    <section class="mt-2 pb-6 px-6 pt-5 bg-white rounded-lg">
                        <h4 class="fs-22 text-heading mb-4">D&eacute;tails suppl&eacute;mentaires</h4>
                        <div class="row">
                            @if (!empty($property->guarantee))
                                <dl class="col-sm-6 mb-0 d-flex">
                                    <dt class="w-110px fs-14 font-weight-500 text-heading pr-2">Caution:</dt>
                                    <dd>@money($property->guarantee) FCFA</dd>
                                </dl>
                            @endif
                            @if (!empty($property->key_money))
                                <dl class="col-sm-6 mb-0 d-flex">
                                    <dt class="w-110px fs-14 font-weight-500 text-heading pr-2">Avance:</dt>
                                    <dd>@money($property->key_money) FCFA</dd>
                                </dl>
                            @endif
                        </div>
                    </section>
                    @if (!empty($property->floor_plan) && Storage::disk('public')->exists('property/floor/'.$property->floor_plan))
                        <section class="mt-2 pb-7 px-6 pt-6 bg-white rounded-lg">
                            <h4 class="fs-22 text-heading mb-6">Plan</h4>
                            <div class="card border-0 shadow-xxs-5 bg-gray-01">
                                <div class="card-body card-body col-sm-6 offset-sm-3 mb-3">
                                    <img src="{{Storage::url('property/floor/'.$property->floor_plan)}}"
                                         class="card-img" alt="Plan">
                                </div>
                            </div>
                        </section>
                    @endif

                    @if (true)
                        <section class="mt-2 pb-7 px-6 pt-6 bg-white rounded-lg">
                            <h4 class="fs-22 text-heading lh-15 mb-5">Retours Client</h4>
                            <div class="card border-0">
                                <div class="card-body p-0">
                                    <div class="row">
                                        <div class="col-sm-6 mb-6 mb-sm-0">
                                            <div class="bg-gray-01 rounded-lg pt-2 px-6 pb-6">
                                                <h5 class="fs-16 lh-2 text-heading mb-6">
                                                    Note
                                                </h5>
                                                <p class="fs-40 text-heading font-weight-bold mb-6 lh-1">{{ number_format($avg_rating ,1)}} <span
                                                        class="fs-18 text-gray-light font-weight-normal">/5</span></p>
                                              @if($avg_rating >=1 && $avg_rating < 2)
                                                    <ul class="list-inline">
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                    </ul>
                                                  @elseif($avg_rating >=2 && $avg_rating < 3)
                                                <ul class="list-inline">
                                                    <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                    <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                    <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                    <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                    <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                </ul>
                                                @elseif($avg_rating >=3 && $avg_rating < 4)
                                                <ul class="list-inline">
                                                    <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                    <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                    <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                    <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                    <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                        <i class="fas fa-star"></i>
                                                    </li>
                                                </ul>
                                                @elseif($avg_rating >=4 && $avg_rating < 5)
                                                    <ul class="list-inline">
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                    </ul>
                                                @elseif($avg_rating >=5 && $avg_rating < 6)
                                                    <ul class="list-inline">
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                        <li class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                    </ul>

                                                  @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-6 pt-3">
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
                                                             style="width: {{ $prFive }}%" aria-valuenow="{{ $prFive }}" aria-valuemin="0"
                                                             aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="text-muted px-1">{{ $prFive }}</div>
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
                                                             style="width: {{ $prFour }}%" aria-valuenow="{{ $prFour }}" aria-valuemin="0"
                                                             aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="text-muted px-1">{{ $prFour }}</div>
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
                                                        <div style="width: {{ $prThree }}%" class="progress-bar bg-warning" role="progressbar"
                                                             aria-valuenow="0" aria-valuemin="{{ $prThree }}"
                                                             aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="text-muted px-1">{{ $prThree }}%</div>
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
                                                        <div class="progress-bar bg-warning" style="width: {{ $prTwo }}%" role="progressbar"
                                                             aria-valuenow="{{ $prTwo }}" aria-valuemin="0"
                                                             aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="text-muted px-1">{{ $prTwo }}%</div>
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
                                                        <div class="progress-bar bg-warning" style="width: {{ $prOne }}%" role="progressbar"
                                                             aria-valuenow="0" aria-valuemin="{{ $prOne }}"
                                                             aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="text-muted px-1">{{ $prOne }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="mt-2 pb-2 px-6 pt-6 bg-white rounded-lg">
                            <div class="card border-0">
                                <div class="card-body p-0">
                                    <h3 class="fs-16 lh-2 text-heading mb-0 d-inline-block pr-4 border-bottom border-primary">
                                        {{ $reviews->count() }} Reviews</h3>
                                   @foreach($reviews as $rv)
                                    <div class="media border-top pt-7 pb-6 d-sm-flex d-block text-sm-left text-center">
                                        <img src="{{ $rv->user->photo_url }}" style="max-width: 100px;" alt="Danny Fox" class="mr-sm-8 mb-4 mb-sm-0">
                                        <div class="media-body">
                                            <div class="row mb-1 align-items-center">
                                                <div class="col-sm-6 mb-2 mb-sm-0">
                                                    <h4 class="mb-0 text-heading fs-14">{{ $rv->user->full_name }}</h4>
                                                </div>
                                                <div class="col-sm-6">
                                                    @if($rv->rating==1)
                                                        <ul class="list-inline d-flex justify-content-sm-end justify-content-center mb-0">
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                        </ul>
                                                    @elseif($rv->rating==2)
                                                        <ul class="list-inline d-flex justify-content-sm-end justify-content-center mb-0">
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                        </ul>
                                                    @elseif($rv->rating==3)
                                                        <ul class="list-inline d-flex justify-content-sm-end justify-content-center mb-0">
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                        </ul>
                                                    @elseif($rv->rating==4)
                                                        <ul class="list-inline d-flex justify-content-sm-end justify-content-center mb-0">
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-black-50 fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                        </ul>
                                                    @elseif($rv->rating==5)
                                                        <ul class="list-inline d-flex justify-content-sm-end justify-content-center mb-0">
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                            <li class="list-inline-item mr-1">
                                                            <span class="text-warning fs-12 lh-2"><i
                                                                    class="fas fa-star"></i></span>
                                                            </li>
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="mb-3 pr-xl-17">{{ $rv->review }}</p>
                                            <div class="d-flex justify-content-sm-start justify-content-center">
                                                <p class="mb-0 text-muted fs-13 lh-1">{{$rv->created_at}}</p>
{{--                                                <a href="#"--}}
{{--                                                   class="mb-0 text-heading border-left border-dark hover-primary lh-1 ml-2 pl-2">Reply</a>--}}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                        <section class="mt-2 pb-7 px-6 pt-6 bg-white rounded-lg">
                            <div class="card border-0">
                                <div class="card-body p-0">
                                    <h3 class="fs-16 lh-2 text-heading mb-4">Write A Review</h3>
                                    <form>
                                        <div class="form-group mb-4 d-flex justify-content-start">
                                            <div class="rate-input">
                                                <input type="radio" id="star5" name="rate" value="5">
                                                <label for="star5" title="text" class="mb-0 mr-1 lh-1">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                                <input type="radio" id="star4" name="rate" value="4">
                                                <label for="star4" title="text" class="mb-0 mr-1 lh-1">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                                <input type="radio" id="star3" name="rate" value="3">
                                                <label for="star3" title="text" class="mb-0 mr-1 lh-1">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                                <input type="radio" id="star2" name="rate" value="2">
                                                <label for="star2" title="text" class="mb-0 mr-1 lh-1">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                                <input type="radio" id="star1" name="rate" value="1">
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
                                            <textarea class="form-control form-control-lg border-0"
                                                      placeholder="Your Review" name="message" rows="5"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-lg btn-primary px-10">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </section>
                        <section class="mt-2 pb-5 px-6 pt-6 bg-white rounded-lg">
                            <h4 class="fs-22 text-heading mb-5">What is Nearby?</h4>
                            <div class="mt-4">
                                <h6 class="mb-0 mt-5"><a href="#"
                                                         class="fs-16 lh-2 text-heading border-bottom border-primary pb-1">Restaurants</a>
                                </h6>
                                <div class="border-top pt-2">
                                    <div class="py-3 border-bottom d-sm-flex justify-content-sm-between">
                                        <div class="media align-items-sm-center d-sm-flex d-block">
                                            <a href="#" class="hover-shine">
                                                <img src="images/single-detail-property-02.jpg"
                                                     class="mr-sm-4 rounded-lg w-sm-90"
                                                     alt="Bacchanal Buffet-Temporarily Closed">
                                            </a>
                                            <div class="mt-sm-0 mt-2">
                                                <h4 class="my-0"><a href="#"
                                                                    class="lh-186 fs-15 text-heading hover-primary">Bacchanal
                                                        Buffet-Temporarily Closed</a></h4>
                                                <p class="lh-186 fs-15 font-weight-500 mb-0">3570 S Las Vegas BlvdLas
                                                    Vegas, NV 89109</p>
                                            </div>
                                        </div>
                                        <div class="text-lg-right mt-lg-0 mt-2">
                                            <p class="mb-2 mb-0 lh-13">120 Reviews</p>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                        </div>
                                    </div>
                                    <div class="py-3 border-bottom d-sm-flex justify-content-sm-between">
                                        <div class="media align-items-sm-center d-sm-flex d-block">
                                            <a href="#" class="hover-shine">
                                                <img src="images/single-detail-property-03.jpg"
                                                     class="mr-sm-4 rounded-lg w-sm-90"
                                                     alt="Bacchanal Buffet-Temporarily Closed">
                                            </a>
                                            <div class="mt-sm-0 mt-2">
                                                <h4 class="my-0"><a href="#"
                                                                    class="lh-186 fs-15 text-heading hover-primary">Bacchanal
                                                        Buffet-Temporarily Closed</a></h4>
                                                <p class="lh-186 fs-15 font-weight-500 mb-0">3084 S Highland DrSte C</p>
                                            </div>
                                        </div>
                                        <div class="text-lg-right mt-lg-0 mt-2">
                                            <p class="mb-2 mb-0 lh-13">120 Reviews</p>
                                            <div class="text-lg-right mt-lg-0 mt-2">
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-3 border-bottom d-sm-flex justify-content-sm-between">
                                        <div class="media align-items-sm-center d-sm-flex d-block">
                                            <a href="#" class="hover-shine">
                                                <img src="images/single-detail-property-04.jpg"
                                                     class="mr-sm-4 rounded-lg w-sm-90"
                                                     alt="Bacchanal Buffet-Temporarily Closed">
                                            </a>
                                            <div class="mt-sm-0 mt-2">
                                                <h4 class="my-0"><a href="#"
                                                                    class="lh-186 fs-15 text-heading hover-primary">Bacchanal
                                                        Buffet-Temporarily Closed</a></h4>
                                                <p class="lh-186 fs-15 font-weight-500 mb-0">3570 S Las Vegas BlvdLas
                                                    Vegas, NV 89109</p>
                                            </div>
                                        </div>
                                        <div class="text-lg-right mt-lg-0 mt-2">
                                            <p class="mb-2 mb-0 lh-13">120 Reviews</p>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="mb-0 mt-5"><a href="#"
                                                         class="fs-16 lh-2 text-heading border-bottom border-primary pb-1">Education</a>
                                </h6>
                                <div class="border-top pt-2">
                                    <div class="py-3 border-bottom d-sm-flex justify-content-sm-between">
                                        <div class="media align-items-sm-center d-sm-flex d-block">
                                            <a href="#" class="hover-shine">
                                                <img src="images/single-detail-property-07.jpg"
                                                     class="mr-sm-4 rounded-lg w-sm-90"
                                                     alt="Bacchanal Buffet-Temporarily Closed">
                                            </a>
                                            <div class="mt-sm-0 mt-2">
                                                <h4 class="my-0"><a href="#"
                                                                    class="lh-186 fs-15 text-heading hover-primary">Safe
                                                        Direction Firearms Training</a></h4>
                                                <p class="lh-186 fs-15 font-weight-500 mb-0">3570 S Las Vegas BlvdLas
                                                    Vegas, NV 89109</p>
                                            </div>
                                        </div>
                                        <div class="text-lg-right mt-lg-0 mt-2">
                                            <p class="mb-2 mb-0 lh-13">120 Reviews</p>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                        </div>
                                    </div>
                                    <div class="py-3 border-bottom d-sm-flex justify-content-sm-between">
                                        <div class="media align-items-sm-center d-sm-flex d-block">
                                            <a href="#" class="hover-shine">
                                                <img src="images/single-detail-property-08.jpg"
                                                     class="mr-sm-4 rounded-lg w-sm-90"
                                                     alt="Bacchanal Buffet-Temporarily Closed">
                                            </a>
                                            <div class="mt-sm-0 mt-2">
                                                <h4 class="my-0"><a href="#"
                                                                    class="lh-186 fs-15 text-heading hover-primary">Rabbi
                                                        Shai Specht-Sandler</a></h4>
                                                <p class="lh-186 fs-15 font-weight-500 mb-0">3084 S Highland DrSte C</p>
                                            </div>
                                        </div>
                                        <div class="text-lg-right mt-lg-0 mt-2">
                                            <p class="mb-2 mb-0 lh-13">120 Reviews</p>
                                            <div class="text-lg-right mt-lg-0 mt-2">
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-3 border-bottom d-sm-flex justify-content-sm-between">
                                        <div class="media align-items-sm-center d-sm-flex d-block">
                                            <a href="#" class="hover-shine">
                                                <img src="images/single-detail-property-09.jpg"
                                                     class="mr-sm-4 rounded-lg w-sm-90"
                                                     alt="Bacchanal Buffet-Temporarily Closed">
                                            </a>
                                            <div class="mt-sm-0 mt-2">
                                                <h4 class="my-0"><a href="#"
                                                                    class="lh-186 fs-15 text-heading hover-primary">Safe
                                                        Direction Firearms Training</a></h4>
                                                <p class="lh-186 fs-15 font-weight-500 mb-0">3570 S Las Vegas BlvdLas
                                                    Vegas, NV 89109</p>
                                            </div>
                                        </div>
                                        <div class="text-lg-right mt-lg-0 mt-2">
                                            <p class="mb-2 mb-0 lh-13">120 Reviews</p>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="mb-0 mt-5"><a href="#"
                                                         class="fs-16 lh-2 text-heading border-bottom border-primary pb-1">Health
                                        & Medical</a></h6>
                                <div class="border-top pt-2">
                                    <div class="py-3 border-bottom d-sm-flex justify-content-sm-between">
                                        <div class="media align-items-sm-center d-sm-flex d-block">
                                            <a href="#" class="hover-shine">
                                                <img src="images/single-detail-property-10.jpg"
                                                     class="mr-sm-4 rounded-lg w-sm-90"
                                                     alt="Bacchanal Buffet-Temporarily Closed">
                                            </a>
                                            <div class="mt-sm-0 mt-2">
                                                <h4 class="my-0"><a href="#"
                                                                    class="lh-186 fs-15 text-heading hover-primary">Coppola
                                                        David F DC & Assoc</a></h4>
                                                <p class="lh-186 fs-15 font-weight-500 mb-0">3570 S Las Vegas BlvdLas
                                                    Vegas, NV 89109</p>
                                            </div>
                                        </div>
                                        <div class="text-lg-right mt-lg-0 mt-2">
                                            <p class="mb-2 mb-0 lh-13">120 Reviews</p>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                            <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm"></i>
                                        </div>
                                    </div>
                                    <div class="py-3 border-bottom d-sm-flex justify-content-sm-between">
                                        <div class="media align-items-sm-center d-sm-flex d-block">
                                            <a href="#" class="hover-shine">
                                                <img src="images/single-detail-property-11.jpg"
                                                     class="mr-sm-4 rounded-lg w-sm-90"
                                                     alt="Bacchanal Buffet-Temporarily Closed">
                                            </a>
                                            <div class="mt-sm-0 mt-2">
                                                <h4 class="my-0"><a href="#"
                                                                    class="lh-186 fs-15 text-heading hover-primary">Elite
                                                        Medical Center</a></h4>
                                                <p class="lh-186 fs-15 font-weight-500 mb-0">3084 S Highland DrSte C</p>
                                            </div>
                                        </div>
                                        <div class="text-lg-right mt-lg-0 mt-2">
                                            <p class="mb-2 mb-0 lh-13">120 Reviews</p>
                                            <div class="text-lg-right mt-lg-0 mt-2">
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-7"></i>
                                                <i class="fas fa-star w-18px h-18 d-inline-flex justify-content-center align-items-center rate-bg-blue text-white fs-12 rounded-sm opacity-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>


                        <section class="mt-2 pb-6 px-6 pt-6 bg-white rounded-lg">
                            <h4 class="fs-22 text-heading mb-6">Location</h4>
                            <div class="position-relative">
                                <div class="position-relative">
                                    <div id="map" class="mapbox-gl map-point-animate"
                                         data-mapbox-access-token="pk.eyJ1IjoiZHVvbmdsaCIsImEiOiJjanJnNHQ4czExMzhyNDVwdWo5bW13ZmtnIn0.f1bmXQsS6o4bzFFJc8RCcQ"
                                         data-mapbox-options='{"center":[-73.9927227, 40.6741035],"setLngLat":[-73.9927227, 40.6741035]}'
                                         data-mapbox-marker='[{"position":[-73.9927227, 40.6741035],"className":"marker","backgroundImage":"images/googlle-market-01.png","backgroundRepeat":"no-repeat","width":"30px","height":"40px"}]'>
                                    </div>
                                    <p class="mb-0 p-3 bg-white shadow rounded-lg position-absolute pos-fixed-bottom mb-4 ml-4 lh-17 z-index-2">
                                        62 Gresham St, Victoria Park <br/>
                                        WA 6100, Australia</p>
                                </div>
                            </div>
                        </section>


                        <section class="mt-2 pb-7 px-6 pt-6 bg-white rounded-lg">
                            <h4 class="fs-22 text-heading mb-6">Similar Homes You May Like</h4>
                            <div class="slick-slider"
                                 data-slick-options='{"slidesToShow": 2, "dots":false,"responsive":[{"breakpoint": 1200,"settings": {"slidesToShow":2,"arrows":false}},{"breakpoint": 992,"settings": {"slidesToShow":2}},{"breakpoint": 768,"settings": {"slidesToShow": 1}},{"breakpoint": 576,"settings": {"slidesToShow": 1}}]}'>
                                <div class="box">
                                    <div class="card shadow-hover-2 =">
                                        <div class="hover-change-image bg-hover-overlay rounded-lg card-img-top">
                                            <img src="images/properties-grid-38.jpg"
                                                 alt="Home in Metric Way">
                                            <div class="card-img-overlay p-2 d-flex flex-column">
                                                <div>
                                                    <span class="badge mr-2 badge-primary">for Sale</span>
                                                </div>
                                                <ul class="list-inline mb-0 mt-auto hover-image">
                                                    <li class="list-inline-item mr-2" data-toggle="tooltip"
                                                        title="9 Images">
                                                        <a href="#" class="text-white hover-primary">
                                                            <i class="far fa-images"></i><span class="pl-1">9</span>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                        <a href="#" class="text-white hover-primary">
                                                            <i class="far fa-play-circle"></i><span
                                                                class="pl-1">2</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body pt-3">
                                            <h2 class="card-title fs-16 lh-2 mb-0"><a href="single-property-1.html"
                                                                                      class="text-dark hover-primary">Home
                                                    in Metric Way</a></h2>
                                            <p class="card-text font-weight-500 text-gray-light mb-2">1421 San Pedro St,
                                                Los Angeles</p>
                                            <ul class="list-inline d-flex mb-0 flex-wrap mr-n4">
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="3 Bedroom">
                                                    <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-bedroom"></use>
                                                    </svg>
                                                    3 Br
                                                </li>
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="3 Bathrooms">
                                                    <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-shower"></use>
                                                    </svg>
                                                    3 Ba
                                                </li>
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="Size">
                                                    <svg class="icon icon-square fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-square"></use>
                                                    </svg>
                                                    2300 Sq.Ft
                                                </li>
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="1 Garage">
                                                    <svg class="icon icon-Garage fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-Garage"></use>
                                                    </svg>
                                                    1 Gr
                                                </li>
                                            </ul>
                                        </div>
                                        <div
                                            class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
                                            <p class="fs-17 font-weight-bold text-heading mb-0">$1.250.000</p>
                                            <ul class="list-inline mb-0">
                                                <li class="list-inline-item">
                                                    <a href="#"
                                                       class="w-40px h-40 border rounded-circle d-inline-flex align-items-center justify-content-center text-secondary bg-accent border-accent"
                                                       data-toggle="tooltip" title="Wishlist"><i
                                                            class="fas fa-heart"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="#"
                                                       class="w-40px h-40 border rounded-circle d-inline-flex align-items-center justify-content-center text-body hover-secondary bg-hover-accent border-hover-accent"
                                                       data-toggle="tooltip" title="Compare"><i
                                                            class="fas fa-exchange-alt"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="box">
                                    <div class="card shadow-hover-2 =">
                                        <div class="hover-change-image bg-hover-overlay rounded-lg card-img-top">
                                            <img src="images/properties-grid-01.jpg"
                                                 alt="Garden Gingerbread House">
                                            <div class="card-img-overlay p-2 d-flex flex-column">
                                                <div>
                                                    <span class="badge mr-2 badge-orange">featured</span>
                                                    <span class="badge mr-2 badge-indigo">for Sale</span>
                                                </div>
                                                <ul class="list-inline mb-0 mt-auto hover-image">
                                                    <li class="list-inline-item mr-2" data-toggle="tooltip"
                                                        title="9 Images">
                                                        <a href="#" class="text-white hover-primary">
                                                            <i class="far fa-images"></i><span class="pl-1">9</span>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                        <a href="#" class="text-white hover-primary">
                                                            <i class="far fa-play-circle"></i><span
                                                                class="pl-1">2</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body pt-3">
                                            <h2 class="card-title fs-16 lh-2 mb-0"><a href="single-property-1.html"
                                                                                      class="text-dark hover-primary">Garden
                                                    Gingerbread House</a></h2>
                                            <p class="card-text font-weight-500 text-gray-light mb-2">1421 San Pedro St,
                                                Los Angeles</p>
                                            <ul class="list-inline d-flex mb-0 flex-wrap mr-n4">
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="3 Bedroom">
                                                    <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-bedroom"></use>
                                                    </svg>
                                                    3 Br
                                                </li>
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="3 Bathrooms">
                                                    <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-shower"></use>
                                                    </svg>
                                                    3 Ba
                                                </li>
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="Size">
                                                    <svg class="icon icon-square fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-square"></use>
                                                    </svg>
                                                    2300 Sq.Ft
                                                </li>
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="1 Garage">
                                                    <svg class="icon icon-Garage fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-Garage"></use>
                                                    </svg>
                                                    1 Gr
                                                </li>
                                            </ul>
                                        </div>
                                        <div
                                            class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
                                            <p class="fs-17 font-weight-bold text-heading mb-0">$550<span
                                                    class="text-gray-light font-weight-500 fs-14"> / month</span></p>
                                            <ul class="list-inline mb-0">
                                                <li class="list-inline-item">
                                                    <a href="#"
                                                       class="w-40px h-40 border rounded-circle d-inline-flex align-items-center justify-content-center text-body hover-secondary bg-hover-accent border-hover-accent"
                                                       data-toggle="tooltip" title="Wishlist"><i
                                                            class="far fa-heart"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="#"
                                                       class="w-40px h-40 border rounded-circle d-inline-flex align-items-center justify-content-center text-body hover-secondary bg-hover-accent border-hover-accent"
                                                       data-toggle="tooltip" title="Compare"><i
                                                            class="fas fa-exchange-alt"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="box">
                                    <div class="card shadow-hover-2 =">
                                        <div class="hover-change-image bg-hover-overlay rounded-lg card-img-top">
                                            <img src="images/properties-grid-02.jpg"
                                                 alt="Affordable Urban House">
                                            <div class="card-img-overlay p-2 d-flex flex-column">
                                                <div>
                                                    <span class="badge mr-2 badge-primary">for Sale</span>
                                                </div>
                                                <ul class="list-inline mb-0 mt-auto hover-image">
                                                    <li class="list-inline-item mr-2" data-toggle="tooltip"
                                                        title="9 Images">
                                                        <a href="#" class="text-white hover-primary">
                                                            <i class="far fa-images"></i><span class="pl-1">9</span>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                        <a href="#" class="text-white hover-primary">
                                                            <i class="far fa-play-circle"></i><span
                                                                class="pl-1">2</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body pt-3">
                                            <h2 class="card-title fs-16 lh-2 mb-0"><a href="single-property-1.html"
                                                                                      class="text-dark hover-primary">Affordable
                                                    Urban House</a></h2>
                                            <p class="card-text font-weight-500 text-gray-light mb-2">1421 San Pedro St,
                                                Los Angeles</p>
                                            <ul class="list-inline d-flex mb-0 flex-wrap mr-n4">
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="3 Bedroom">
                                                    <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-bedroom"></use>
                                                    </svg>
                                                    3 Br
                                                </li>
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="3 Bathrooms">
                                                    <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-shower"></use>
                                                    </svg>
                                                    3 Ba
                                                </li>
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="Size">
                                                    <svg class="icon icon-square fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-square"></use>
                                                    </svg>
                                                    2300 Sq.Ft
                                                </li>
                                                <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-4"
                                                    data-toggle="tooltip" title="1 Garage">
                                                    <svg class="icon icon-Garage fs-18 text-primary mr-1">
                                                        <use xlink:href="#icon-Garage"></use>
                                                    </svg>
                                                    1 Gr
                                                </li>
                                            </ul>
                                        </div>
                                        <div
                                            class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
                                            <p class="fs-17 font-weight-bold text-heading mb-0">$1.250.000</p>
                                            <ul class="list-inline mb-0">
                                                <li class="list-inline-item">
                                                    <a href="#"
                                                       class="w-40px h-40 border rounded-circle d-inline-flex align-items-center justify-content-center text-body hover-secondary bg-hover-accent border-hover-accent"
                                                       data-toggle="tooltip" title="Wishlist"><i
                                                            class="far fa-heart"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="#"
                                                       class="w-40px h-40 border rounded-circle d-inline-flex align-items-center justify-content-center text-body hover-secondary bg-hover-accent border-hover-accent"
                                                       data-toggle="tooltip" title="Compare"><i
                                                            class="fas fa-exchange-alt"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif
                </article>
                <aside class="col-lg-4 pl-xl-4 primary-sidebar sidebar-sticky" id="sidebar">
                    <div class="primary-sidebar-inner">
                        <div class="bg-white rounded-lg py-lg-6 pl-lg-6 pr-lg-3 p-4">
                            <div class="row">
                                @if (!empty($property->bedroom))
                                    <div class="col-6 mb-3">
                                        <div class="media">
                                            <div class="p-2 shadow-xxs-1 rounded-lg mr-2 lh-1">
                                                <svg class="icon icon-bedroom fs-18 text-primary">
                                                    <use xlink:href="#icon-bedroom"></use>
                                                </svg>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="fs-13 font-weight-normal mb-0">Chambres</h5>
                                                <p class="mb-0 fs-13 font-weight-bold text-dark">{{ $property->bedroom }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (!empty($property->bathroom))
                                    <div class="col-6 mb-3">
                                        <div class="media">
                                            <div class="p-2 shadow-xxs-1 rounded-lg mr-2 lh-1">
                                                <svg class="icon icon-shower fs-18 text-primary">
                                                    <use xlink:href="#icon-shower"></use>
                                                </svg>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="fs-13 font-weight-normal mb-0">Salles de bain</h5>
                                                <p class="mb-0 fs-13 font-weight-bold text-dark">{{ $property->bathroom}}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-6 mb-3">
                                    <div class="media">
                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2 lh-1">
                                            <svg class="icon icon-square fs-18 text-primary">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="fs-13 font-weight-normal mb-0">Surface</h5>
                                            <p class="mb-0 fs-13 font-weight-bold text-dark">{{ $property->area }}
                                                m2</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mr-xl-2">
                                @if ($alreadyContacted)
                                    <div
                                        class="badge badge-primary btn-lg btn-block rounded border border-hover-primary hover-white">
                                        Requ&ecirc;te re&ccedil;ue
                                    </div>
                                @else
                                    <button href="#"
                                            class="btn btn-outline-primary btn-lg btn-block rounded border text-body border-hover-primary hover-white"
                                            data-toggle="modal" data-target="#modal-messenger">Contacter l'Agent(e)
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="bg-white mt-2 rounded-lg py-lg-6 pl-lg-6 pr-lg-3 p-4">
                            @auth()
                                @if($property->checkIfAlreadyRated(auth()->user()->id))
                                    <div class="my-rating-4"  data-rating="{{ $property->checkIfAlreadyRated(auth()->user()->id)->rating }}"></div>
                                    <div>
                                        <p>{{ $property->checkIfAlreadyRated(auth()->user()->id)->review }}</p>
                                    </div>

                                @else

                            <form method="post" id="form_create_demande" class="form-xhr blockui"
                                  action="{{ route('public.review.store', $property) }}">

                                @csrf
                                <input type="hidden" name="type" value="property">
                                <input type="hidden" name="rating" id="user_rating">
                                <div class="form-group">
                                    <label>Rating</label>
                                    <div class="my-rating-4" data-rating="1"></div>

                                </div>

                                <div class="form-group">
                                    <label>Review</label>
                                    <textarea name="review" class="form-control"></textarea>
                                </div>

                                <button type="submit" class="btn btn-success">Envoyer</button>
                            </form>
                                    @endif
                            @else
                                <a href="{{ route('login') }}"
                                        class="btn btn-outline-primary btn-lg btn-block rounded border text-body border-hover-primary hover-white">Connectez-vous pour évaluer


                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="modal fade" id="modal-messenger" tabindex="-1" aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" id="form_create_demande" class="form-xhr blockui"
                                      action="{{ route('public.properties.book', $property) }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Interess&eacute;(e) par ce
                                            bien?</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @auth
                                            <input type="hidden" name="user_id" value="{{ auth()->id() }}"/>
                                            <p>
                                                En cliquant sur le bouton ci-dessous, l'agent recevra directement votre
                                                requ&ecirc;te. Vous recevrez un email d&egrave;s qu'il la prendra en
                                                compte et vous pourrez discuter &agrave; partir de l'espace d&eacute;di&eacute;.
                                            </p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">Type:</span>
                                                </div>
                                                <select class="form-control form-select" aria-label="Default select example">
                                                    <option value="1">Demande d'informations</option>
                                                    <option value="@if($property->purpose == "vente") 3 @else 2 @endif">@if($property->purpose == "vente") Demande d'achat @else Demande location @endif</option>
                                                </select>
                                            </div>
                                        @else
                                            <p>
                                                Entrez vos informations pour contacter l'agent en charge de ce bien. Par
                                                la m&ecirc;me occasion, vous recevrez vos identifiants sur l'interface
                                                de contr&ocirc;le de MCK &agrave; partir de laquelle vous pourrez
                                                &eacute;changer avec l'agent directement.
                                            </p>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">Type:</span>
                                                </div>
                                                <select class="form-control form-select" aria-label="Default select example" name="note">
                                                    <option value="1">Demande d'informations</option>
                                                    <option value="@if($property->purpose == "vente") 3 @else 2 @endif">@if($property->purpose == "vente") Demande d'achat @else Demande de location @endif</option>
                                                </select>
                                            </div>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">Nom:</span>
                                                </div>
                                                <input type="text" class="form-control" name="last_name">
                                            </div>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                          id="basic-addon1">Pr&eacute;noms:</span>
                                                </div>
                                                <input type="text" class="form-control" name="first_name">
                                            </div>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">Email:</span>
                                                </div>
                                                <input type="email" class="form-control" name="email">
                                            </div>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">T&eacute;l&eacute;phone:</span>
                                                </div>
                                                <input type="tel" id="phone" class="form-control" name="ph">
                                                <input type="hidden" name="phone_number[mobile]">
                                            </div>
                                        @endauth
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Envoyer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </aside>

            </div>
        </div>
    </div>

@endsection
@section('specific-js')
    <script src="{{ asset('assets/intlinput/js/intlTelInput.js') }}"></script>
    <script src="{{ asset('assets/rating/jquery.star-rating-svg.js') }}"></script>
    <script>
        var input = document.querySelector("#phone");
        if(input) {
            var iti = window.intlTelInput(input, {
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
                initialCountry: "auto",
                // localizedCountries: { 'de': 'Deutschland' },
                nationalMode: true,
                // separateDialCode:true,
                // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                // placeholderNumberType: "MOBILE",
                // preferredCountries: ['cn', 'jp'],
                // separateDialCode: true,
                utilsScript: "{{asset('assets/intlinput/js/utils.js')}}",
            });
            var handleChange = function () {

            };

            // listen to "keyup", but also "change" to update when the user selects a country
            input.addEventListener('change', handleChange);
            input.addEventListener('keyup', handleChange);
        }
        $(".my-rating-4").starRating({
            totalStars: 5,
            useFullStars: true,
            starShape: 'rounded',
            starSize: 30,
            emptyColor: 'lightgray',
            hoverColor: '#eeac05',
            activeColor: '#eeac05',
            useGradient: false,
            callback: function(currentRating, $el){
                // alert('rated ' + currentRating);
                // console.log('DOM element ', $el);
                $('#user_rating').attr('value',currentRating);
                // $('#user_rating').val(currentRating);
            }
        });

        //user_rating
    </script>
    @auth()
    @if($property->checkIfAlreadyRated(auth()->user()->id))
<script>$('.my-rating-4').starRating('setReadOnly', true);</script>
    @endif
    @endauth
        @endsection
