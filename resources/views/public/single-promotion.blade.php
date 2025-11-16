@extends("public.layouts")
@section('title') {{ $promotion->title }} @endsection
@section('specific-css')
    <link href="{{ asset('assets/rating/css/star-rating-svg.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/intlinput/css/intlTelInput.css') }}" type="text/css" rel="stylesheet">
    <style>
        .iti{
            width: 79%;
        }
    </style>
@endsection
@section('content')
    <section class="pb-6 pt-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Accueil</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $promotion->title }}</li>
                </ol>
            </nav>
        </div>
    </section>
    <section class="bg-gray-01 pt-5 pb-13">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 primary-sidebar s" id="sidebar">

                    <div class="card">
                        <div class="card-body">

                            <div class="d-md-flex justify-content-md-between mb-6">
                                <div>
                                    <h2 class="fs-35 font-weight-600 lh-15 text-heading">{{$promotion->title}}</h2>
                                    <p class="mb-0">
                                        <i class="fal fa-map-marker-alt mr-2"></i>{{$promotion->project}} - {{$promotion->developer}} - {{$promotion->fullAddress()}}
                                    </p>
                                    <div class="mt-5">
                                        {!! $promotion->description !!}
                                    </div>
                                </div>
                            </div><section>
                        <div class="galleries position-relative">
                            <div class="position-absolute pos-fixed-top-right z-index-3">
                                <ul class="list-inline pt-4 pr-5">
                                </ul>
                            </div>
                            <div class="slick-slider slider-for-01 arrow-haft-inner mx-0"
                                 data-slick-options='{"slidesToShow": 1, "autoplay":false,"dots":false,"arrows":false,"asNavFor": ".slider-nav-01"}'>
                                @if (!($promotion->images->isEmpty()))
                                    @foreach($promotion->images as $image)
                                        @if (Storage::disk('public')->exists('promotion/gallery/'.$image->name))
                                            <div class="box px-0">
                                                <div class="item item-size-3-2">
                                                    <div class="card p-0 hover-change-image">
                                                        <a href="{{Storage::url('promotion/gallery/'.$image->name)}}"
                                                           class="card-img" data-gtf-mfp="true" data-gallery-id="04"
                                                           style="background-image:url('{{Storage::url('promotion/gallery/'.$image->name)}}')">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <div class="slick-slider slider-nav-01 mt-4 mx-n1 arrow-haft-inner"
                                 data-slick-options='{"slidesToShow": 5, "autoplay":false,"dots":false,"arrows":false,"asNavFor": ".slider-for-01","focusOnSelect": true,"responsive":[{"breakpoint": 768,"settings": {"slidesToShow": 4}},{"breakpoint": 576,"settings": {"slidesToShow": 2}}]}'>
                                @if (!($promotion->images->isEmpty()))
                                    @foreach($promotion->images as $image)
                                        @if (Storage::disk('public')->exists('promotion/gallery/'.$image->name))
                                            <div class="box pb-6 px-0">
                                                <div class="bg-hover-white p-1 shadow-hover-xs-3 h-100 rounded-lg">
                                                    <img src="{{Storage::url('promotion/gallery/'.$image->name)}}"
                                                         alt="{{ $promotion->title}}" class="h-100 w-100 rounded-lg">
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </section>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-6 mb-lg-0">
                    <div class="primary-sidebar-inner">
                        <div class="card mb-4">
                            <div class="card-body px-6 pt-5 pb-6">
                                <button type="button" class="btn btn-primary btn-lg btn-block shadow-none mb-2"
                                    data-toggle="modal" data-target="#bookingModal">B&eacute;n&eacute;ficier de cette promotion</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Demander le promotion --}}

    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="form_create_demande" class="form-xhr blockui" action="{{ route('public.promotions.book', $promotion) }}">
            @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Interess&eacute;(e) par cette promotion?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <div class="modal-body">
            <p>
              Entrez vos informations pour souscrire &agrave; cette promotion.
            </p>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">Nom:</span>
                  </div>
                  <input type="text" class="form-control" name="name">
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
                  <input type="tel" class="form-control" name="ph" id="phone">
                    <input type="hidden" name="phone_number[phone]">
                </div>
                <div class="mb-3">
                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes" class="form-control form-control-lg border-0 form-control-textarea" ></textarea>
                </div>
            </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Envoyer</button>
          </div>
            </form>
        </div>
      </div>
    </div>

@endsection
@section('specific-js')
    <script src="/vendors/formrepeater/formrepeater.js"></script>

    <script src="{{ asset('assets/rating/jquery.star-rating-svg.js') }}"></script>
    <script src="{{ asset('assets/intlinput/js/intlTelInput.js') }}"></script>
    <script>
        var input = document.querySelector("#phone");
        if (input) {
            var iti = window.intlTelInput(input, {
                hiddenInput: "phone",
                initialCountry: "ci",
                nationalMode: true,
                utilsScript: "{{asset('assets/intlinput/js/utils.js')}}",
            });
            var handleChange = function () {

            };

            // listen to "keyup", but also "change" to update when the user selects a country
            input.addEventListener('change', handleChange);
            input.addEventListener('keyup', handleChange);
        }
    </script>
@endsection

