@extends('public.layouts')
@section('title')
    {{ $service->label }} - Services
@endsection
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
    <section class="pb-6 pt-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('public.services.index') }}">Tous les Services</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $service->label }}</li>
                </ol>
            </nav>
        </div>
    </section>
    <section class="bg-gray-01 pt-5 pb-13">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 primary-sidebar sidebar-sticky" id="sidebar">

                    <div class="card lh-214">
                        <div class="card-body">
                            <div class="row mt-5">
                                <div class="media">
                                    @if (!empty($service->image))
                                        <img class="rounded-lg d-block mr-2 ml-2" src="{{ $service->image }}"
                                            alt="{{ $service->label }}">
                                    @endif
                                </div>
                                <div class="media-body">
                                    <h2 class="fs-30 text-dark font-weight-600 lh-16 mb-0">
                                        {{ $service->label }}
                                    </h2>
                                    <h3 class="fs-22 text-heading lh-15 mt-5">Description</h3>
                                    <div>
                                        {!! $service->description !!}
                                    </div>
                                </div>
                            </div>
                            <h3 class="fs-22 text-heading lh-15 mt-7 mb-3">Comment demander un service?</h3>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="card text-center">
                                        <div class="card-header">
                                            &Eacute;tape 1
                                        </div>
                                        <div class="card-body">
                                            <samp class="card-text">Choisissez le Service qui vous convient</samp>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card text-center">
                                        <div class="card-header">
                                            &Eacute;tape 2
                                        </div>
                                        <div class="card-body">
                                            <samp class="card-text">Envoyez une Demande de Service</samp>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card text-center">
                                        <div class="card-header">
                                            &Eacute;tape 3
                                        </div>
                                        <div class="card-body">
                                            <samp class="card-text">Vous &ecirc;tes mis en relation avec un
                                                sp&eacute;cialiste</samp>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (!empty($newVersion))
                                <h3 class="fs-22 text-heading lh-15 mt-5 mb-3">Evaluations</h3>
                                <div class="card border-0 mb-4">
                                    <div class="card-body p-0">
                                        <div class="row">
                                            <div class="col-sm-6 mb-6 mb-sm-0">
                                                <div class="bg-gray-01 rounded-lg pt-2 px-6 pb-6">
                                                    <h5 class="fs-16 lh-2 text-heading mb-6">
                                                        Moyenne
                                                    </h5>
                                                    @php
                                                        $avis_actifs = $service->avis->where('actif', true);
                                                        $avg = $avis_actifs->avg('note');
                                                    @endphp
                                                    <p class="fs-40 text-heading font-weight-bold mb-6 lh-1">
                                                        {{ number_format($avg, 1, ',', ' ') }} <span
                                                            class="fs-18 text-gray-light font-weight-normal">/5</span></p>
                                                    <ul class="list-inline">
                                                        {!! str_repeat(
                                                            '<li
                                                                                                                    class="list-inline-item bg-warning text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                                                                                    <i class="fas fa-star"></i>
                                                                                                                </li>',
                                                            floor($avg),
                                                        ) !!}
                                                        {!! str_repeat(
                                                            '<li
                                                                                                                    class="list-inline-item bg-gray-04 text-white w-46px h-46 rounded-lg d-inline-flex align-items-center justify-content-center fs-18 mb-1">
                                                                                                                    <i class="fas fa-star"></i>
                                                                                                                </li>',
                                                            5 - floor($avg),
                                                        ) !!}

                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 pt-3">
                                                <h5 class="fs-16 lh-2 text-heading mb-5">
                                                    Répartition
                                                </h5>
                                                @for ($j = 5; $j > 0; $j--)
                                                    @php
                                                        $pct = number_format(($avis_actifs->where('note', $j)->count() * 100) / max($avis_actifs->count(), 1), 0, ',', ' ');
                                                    @endphp
                                                    <div class="d-flex align-items-center mx-n1">
                                                        <ul class="list-inline d-flex px-1 mb-0">
                                                            {!! str_repeat(
                                                                '<li class="list-inline-item text-warning mr-1">
                                                                                                                        <i class="fas fa-star"></i>
                                                                                                                    </li>',
                                                                $j,
                                                            ) !!}
                                                            {!! str_repeat(
                                                                '<li class="list-inline-item text-border mr-1">
                                                                                                                        <i class="fas fa-star"></i>
                                                                                                                    </li>',
                                                                5 - $j,
                                                            ) !!}
                                                        </ul>
                                                        <div class="d-block w-100 px-1">
                                                            <div class="progress rating-progress">
                                                                <div class="progress-bar bg-warning" role="progressbar"
                                                                    style="width: {{ $pct }}%"
                                                                    aria-valuenow="{{ $pct }}" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="text-muted px-1">{{ $pct }}%</div>
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="fs-22 text-heading lh-15 mb-6">Avis
                                    ({{ $service->avis->where('actif', true)->count() }})</h3>

                                @forelse($service->avis->where('actif', true)->sortByDesc('id') as $avis)
                                    <div class="media mb-6 pb-5 border-bottom">
                                        <div class="media-body">
                                            <ul class="list-inline mb-0">
                                                <li class="list-inline-item fs-13 text-heading font-weight-500">
                                                    {{ $avis->note }}/5</li>
                                                <li class="list-inline-item fs-13 text-heading font-weight-500 mr-1">
                                                    <ul class="list-inline mb-0">
                                                        {!! str_repeat(
                                                            '<li class="list-inline-item mr-0">
                                                                                                                <span class="text-warning fs-12 lh-2"><i class="fas fa-star"></i></span>
                                                                                                            </li>',
                                                            $avis->note,
                                                        ) !!}
                                                        {!! str_repeat(
                                                            '<li class="list-inline-item mr-0">
                                                                                                                <span class="fs-12 lh-2"><i class="fas fa-star"></i></span>
                                                                                                            </li>',
                                                            5 - $avis->note,
                                                        ) !!}
                                                    </ul>
                                                </li>
                                            </ul>
                                            <p class="text-heading fs-16 font-weight-500 mb-0">{{ $avis->nom }}</p>
                                            <p class="mb-4">
                                                {!! nl2br(htmlspecialchars($avis->comment)) !!}
                                            </p>
                                            <ul class="list-inline">
                                                <li class="list-inline-item text-muted">
                                                    {{ Carbon::parse($avis->created_at)->isoformat('DD MMM YYYY à H:mm') }}<span
                                                        class="d-inline-block mx-2">|</span> <span class="text-primary">Avis
                                                        vérifié</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @empty
                                    <div class="media mb-6 pb-5 border-bottom">
                                        <div class="text-center"> <em>Il n'y a rien à afficher pour le moment.</em> </div>
                                    </div>
                                @endforelse
                                <div class="card border-0 blockui">
                                    <div class="card-body p-0">
                                        <h3 class="fs-16 lh-2 text-heading mb-4">Soumettre mon avis</h3>
                                        <form method="post" action="{{ route('public.services.rate', $service) }}"
                                            class="form-xhr">
                                            @csrf
                                            <input type="hidden" name="service_id" value="{{ $service->id }}" />
                                            <div class="form-group mb-4 d-flex justify-content-start">
                                                <div class="rate-input">
                                                    @for ($i = 5; $i > 0; $i--)
                                                        <input type="radio" id="star{{ $i }}" name="note"
                                                            value="{{ $i }}" />
                                                        <label for="star{{ $i }}" data-toggle="tooltip"
                                                            title="{{ $i }} : étoile{{ $i > 1 ? 's' : '' }}"
                                                            class="mb-0 mr-1 lh-1 choose-stars">
                                                            <i class="fas fa-star"></i>
                                                        </label>
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group mb-4">
                                                        <input placeholder="Nom & Prénom(s)"
                                                            class="form-control form-control-lg border-0" type="text"
                                                            name="nom" maxlength="100"
                                                            @auth value="{{ auth()->user()->full_name }}" @endauth
                                                            required />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group mb-4">
                                                        <input type="email" placeholder="E-mail" name="email"
                                                            class="form-control form-control-lg border-0" maxlength="64"
                                                            @auth value="{{ auth()->user()->email }}" @endauth required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-6">
                                                <textarea class="form-control form-control-lg border-0" placeholder="Que voulez-vous dire ?" name="comment"
                                                    rows="5" required></textarea>
                                            </div>
                                            <button type="submit"
                                                class="btn btn-lg btn-primary px-10 mb-2">Soumettre</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                        {{-- carousel pour l'affichage de la gallerie  --}}
                        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                            </ol>
                            <div class="carousel-inner" style="height:300px;">
                                    <div class="carousel-item active">
                                        <img src="/images/logo.png" class="img-fluid rounded"
                                            style="height: 100%; width:100%;" alt="Responsive image">
                                    </div>
                                @foreach (App\Models\gallerie::where('idService', $service->id)->get() as $img)
                                    <div class="carousel-item">
                                        <img src="{{ $img->image }}" class="img-fluid rounded"
                                            style="height: 100%; width:100%;" alt="Responsive image">
                                    </div>
                                @endforeach

                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button"
                                data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Précedent</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button"
                                data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Suivant</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-6 mb-lg-0">

                    <div class="primary-sidebar-inner">
                        <div class="card mb-4">
                            <div class="card-body px-6 pt-5 pb-6">
                                <button type="button" class="btn btn-primary btn-lg btn-block shadow-none mb-2"
                                    data-toggle="modal" data-target="#bookingModal">Demander ce service</button>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-body px-6 py-5">
                                <h4 class="card-title fs-16 lh-2 text-dark mb-3">D'autres services</h4>
                                <ul class="list-inline mb-0">
                                    @foreach ($services as $s)
                                        <li class="list-inline-item mb-2">
                                            <a href="{{ route('public.services.show', ['service' => $s, 'slug' => $s->slug]) }}"
                                                class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">{{ $s->label }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="bg-white mt-2 rounded-lg py-lg-6 pl-lg-6 pr-lg-3 p-4">
                            @auth()
                                @if ($service->checkIfAlreadyRated(auth()->user()->id))
                                    <div class="my-rating-4"
                                        data-rating="{{ $service->checkIfAlreadyRated(auth()->user()->id)->rating }}"></div>
                                    <div>
                                        <p>{{ $service->checkIfAlreadyRated(auth()->user()->id)->review }}</p>
                                    </div>
                                @else
                                    <form method="post" id="form_create_demande" class="form-xhr blockui"
                                        action="{{ route('public.review.store', $service) }}">

                                        @csrf
                                        <input type="hidden" name="type" value="service">
                                        <input type="hidden" name="rating" id="user_rating">
                                        <div class="form-group">
                                            <label>Note / 5</label>
                                            <div class="my-rating-4" data-rating="1"></div>

                                        </div>

                                        <div class="form-group">
                                            <label>Retour d'expérience</label>
                                            <textarea name="review" class="form-control"></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-success">Envoyer</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}"
                                    class="btn btn-outline-primary btn-lg btn-block rounded border text-body border-hover-primary hover-white">Connectez-vous
                                    pour évaluer


                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Demander le service --}}

    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="form_create_demande" class="form-xhr blockui"
                    action="{{ route('public.services.book', $service) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Interess&eacute;(e) par ce service?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @auth
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}" />
                            <p>
                                En cliquant sur le bouton ci-dessous, le prestataire recevra directement votre requ&ecirc;te.
                                Vous recevrez un email d&egrave;s qu'il la prendra en compte et vous pourrez discuter &agrave;
                                partir de l'espace d&eacute;di&eacute;.
                            </p>
                        @else
                            <p>
                                Entrez vos informations pour contacter le prestataire en charge de ce bien. Par la m&ecirc;me
                                occasion, vous recevrez vos identifiants sur l'interface de contr&ocirc;le de MCK &agrave;
                                partir de laquelle vous pourrez &eacute;changer avec le prestataire directement.
                            </p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Nom:</span>
                                </div>
                                <input type="text" class="form-control" name="last_name">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Pr&eacute;noms:</span>
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
                                <input type="tel" class="form-control" name="ph" id="phone">
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

@endsection
@section('specific-js')
    <script src="/vendors/formrepeater/formrepeater.js"></script>

    <script src="{{ asset('assets/rating/jquery.star-rating-svg.js') }}"></script>
    <script src="{{ asset('assets/intlinput/js/intlTelInput.js') }}"></script>
    <script>
        var input = document.querySelector("#phone");
        if (input) {
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
                initialCountry: "ci",
                // localizedCountries: { 'de': 'Deutschland' },
                nationalMode: true,
                // separateDialCode:true,
                // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                // placeholderNumberType: "MOBILE",
                // preferredCountries: ['cn', 'jp'],
                // separateDialCode: true,
                utilsScript: "{{ asset('assets/intlinput/js/utils.js') }}",
            });
            var handleChange = function() {

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
            callback: function(currentRating, $el) {
                // alert('rated ' + currentRating);
                // console.log('DOM element ', $el);
                $('#user_rating').attr('value', currentRating);
                // $('#user_rating').val(currentRating);
            }
        });
    </script>
    <script>
        jQuery(function() {
            $('.choose-stars').click(function() {
                $('#' + $(this).attr('for')).prop('checked', true);
            });

            $('.form-repeaters').each(function(el) {
                $(this).repeater({
                    initEmpty: false,
                    show: function() {
                        this.querySelector('label').innerText = "Choisir un fichier";
                        this.querySelector('.remove-file').value = "";
                        $(this).slideDown();
                        $('[data-toggle="tooltip"]').tooltip('dispose');
                        $('[data-toggle="tooltip"]').tooltip();
                    },

                    hide: function(deleteElement) {
                        // let file = this.querySelector('.remove-file').value;
                        $(this).slideUp(deleteElement);
                    }
                });
            })
        });
    </script>
    @auth
        @if ($service->checkIfAlreadyRated(auth()->user()->id))
            <script>
                $('.my-rating-4').starRating('setReadOnly', true);
            </script>
        @endif
    @endauth
@endsection
