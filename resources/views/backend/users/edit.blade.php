@extends('layouts.master')
@section('title') Mon Profil @endsection
@section('css')
    <link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
    <link href="/vendors/intl-tel-input/css/intlTelInput.min.css" rel="stylesheet">
    <link href="{{ asset('assets/intlinput/css/intlTelInput.css') }}" type="text/css" rel="stylesheet">
    <style>
        .iti{
            width: 100%;
        }
    </style>
@endsection
@section('content')

<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg">
        <img src="{{ URL::asset('assets/images/profile-bg.jpg') }}" class="profile-wid-img" alt="">
    </div>
</div>
<div class="pt-4">
    <div class="row g-4">

        <div class="col">
            <div class="p-2">
                <h3 class="text-white mb-1">@if(Auth::user()->email == $user->email) Mon Profil @else {{ $user->getFullNameAttribute() }} @endif</h3>
            </div>
        </div>

    </div>
</div>

<form method="post" action="{{route('users.update', $user)}}" enctype="multipart/form-data" class="form-xhr">
    @csrf
    <input type="hidden" name="_method" value="PATCH" />

    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
        <div class="row g-4">
            <div class="col mb-4">

                <div class="alert alert-info border-0 rounded-0 m-0 d-flex align-items-center"
                    role="alert">
                    <i data-feather="alert-triangle"
                        class="text-info me-2 icon-sm"></i>
                    <div class="flex-grow-1 text-truncate">
                     @if(Auth::user()->email == $user->email) Bienvenue, @endif  @if(Auth::user()->email == $user->email) <strong>{{Auth::user()->getFullNameAttribute()}}</strong> ! Mettez &agrave; jour votre profil @else Modifiez le profil <strong>{{Auth::user()->getFullNameAttribute()}}</strong> @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        @if($user->getIsAdminAttribute())
            <div class="col-xxl-3">
                <div class="card mt-n5">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                                <img src="/public{{$user->photo_url}}"
                                    class="rounded-circle avatar-xl img-thumbnail user-profile-image" name="photo_file" alt="user-profile-image">
                                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                    <input id="profile-img-file-input" type="file"
                                        class="profile-img-file-input" name="photo_file" accept="image/*">
                                    <label for="profile-img-file-input"
                                        class="profile-photo-edit avatar-xs">
                                        <span class="avatar-title rounded-circle bg-light text-body">
                                            <i class="ri-camera-fill"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <h5 class="fs-16 mb-1">{{$user->full_name}}</h5>
                        </div>
                    </div>
                </div>
                <!--end card-->
            </div>
        @endif
        <!--end col-->
        <div class="col-xxl-9">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails"
                                role="tab">
                                <i class="fas fa-home"></i>
                                D&eacute;tails Perso
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                <i class="far fa-user"></i>
                                Mot de passe
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="firstnameInput" class="form-label">Pr&eacute;noms</label>
                                        <input type="text" class="form-control" id="firstnameInput" name="prenoms"
                                            placeholder="Vos pr&eacute;noms" value="{{$user->prenoms}}">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="lastnameInput" class="form-label">Nom</label>
                                        <input type="text" class="form-control" id="lastnameInput" name="nom"
                                            placeholder="Votre nom" value="{{$user->nom}}">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Mobile</label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Votre num&eacute;ro mobile" value="{{$user->mobile}}">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phonenumberInput" class="form-label">Fixe</label>
                                        <input type="text" class="form-control" id="numberInput" name="telephone" placeholder="Votre num&eacute;ro fixe" value="{{$user->telephone}}">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="emailInput" class="form-label">Adresse email</label>
                                        <input type="email" class="form-control" id="emailInput" placeholder="Votre adresse email" name="email" value="{{$user->email}}" readonly>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Titre / Poste</label>
                                        <input type="text" class="form-control" id="titre" placeholder="Votre titre ou poste" name="titre" value="{{$user->titre}}">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="cityInput" class="form-label">Ville</label>
                                        <input type="text" class="form-control" id="cityInput" placeholder="Votre ville" name="ville" value="{{$user->ville}}" />
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="countryInput" class="form-label">Pays</label>
                                        <select class="form-control" data-choices data-choices-search-false name="pays" id="pays">
                                        @foreach(App\Models\Country::all()->sortBy('nom') as $p)
                                        @php
                                            $defaultPays = $user->code_pays ?? "CI";
                                            echo $defaultPays;
                                        @endphp
                                            <option value="{{$p->code}}" @if($p->code === $defaultPays) selected @endif>{{$p->nom}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="mb-3 pb-2">
                                        <label for="exampleFormControlTextarea" class="form-label">Pr&eacute;sentation</label>
                                        <textarea class="form-control" id="introduction" name="introduction" placeholder="Votre pr&eacute;sentation" rows="3">{{$user->introduction}}</textarea>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-success">Enregistrer</button>
                                        <button type="button" class="btn btn-soft-success">Annuler</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                                <div class="row g-2">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="oldpasswordInput" class="form-label">Mot de passe actuel*</label>
                                            <input type="password" class="form-control" id="oldpasswordInput" name="old_password">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="newpasswordInput" class="form-label">Nouveau Mot de passe*</label>
                                            <input type="password" class="form-control" id="newpasswordInput" name="new_password">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="confirmpasswordInput" class="form-label">Confirmation du nouveau mot de passe*</label>
                                            <input type="password" class="form-control" id="confirmpasswordInput" name="new_password_confirmation">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <a href="{{ route('password.request') }}" class="link-primary text-decoration-underline">Mot de passe oubli&eacute; ?</a>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success">Modifier mot de passe</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
</form>

<!--end row-->
@endsection
@section('script')
        <script src="/vendors/jquery.min.js"></script>
        <script src="/vendors/jquery-ui/jquery-ui.min.js"></script>
        <script src="/vendors/formrepeater/formrepeater.js"></script>
        <script src="/vendors/slick/slick.min.js"></script>
        <script src="/vendors/waypoints/jquery.waypoints.min.js"></script>
        <script src="/vendors/hc-sticky/hc-sticky.min.js"></script>

        <script src="/vendors/waitMe/waitMe.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="/vendors/sweetalert2.all.min.js"></script>
        <script src="/vendors/config.js"></script>
        <script src="/js/backoffice.js"></script>
        <script src="{{ URL::asset('assets/js/pages/profile-setting.init.js') }}"></script>
        <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
        <script src="{{ asset('assets/intlinput/js/intlTelInput.js') }}"></script>
        <script>
            $(document).ready(function(){
                $('#pointsCredit').click(function(){
                    $('#creditPointsModal').modal('show')
                });
            });
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
@endsection
