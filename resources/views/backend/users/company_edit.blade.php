@extends('layouts.master')
@section('title') Profil de Votre Compagnie @endsection
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
                <h3 class="text-white mb-1">Profil de la Compagnie</h3>
            </div>
        </div>
    </div>
</div>

<form method="post" action="{{route('company.update')}}" enctype="multipart/form-data" class="form-xhr">
    @csrf
    <input type="hidden" name="_method" value="PATCH" />

<div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
    <div class="row g-4">
        <div class="col mb-4"></div>
    </div>
</div>
<div class="row">
    <div class="col-xxl-3">
        <div class="card mt-n5">
            <div class="card-body p-4">
                <div class="text-center">
                    <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                        <img src="{{$company->logo_url ?? $user->photo_url}}" class="rounded-circle avatar-xl img-thumbnail user-profile-image" name="photo_file" alt="user-profile-image">
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                            <input id="profile-img-file-input" type="file" class="profile-img-file-input" name="photo_file" accept="image/*">
                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                <span class="avatar-title rounded-circle bg-light text-body">
                                    <i class="ri-camera-fill"></i>
                                </span>
                            </label>
                        </div>
                    </div>
                    <h5 class="fs-16 mb-1">Logo</h5>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
    <div class="col-xxl-9">
        <div class="card mt-xxl-n5">
            <div class="card-header">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0"
                    role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                            <i class="fas fa-home"></i>
                            D&eacute;tails
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#docDetails" role="tab">
                            <i class="fas fa-home"></i>
                            Documents / Admin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#socialDetails" role="tab">
                            <i class="fas fa-home"></i>
                            R&eacute;seaux Sociaux
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
                                    <label for="firstnameInput" class="form-label">D&eacute;nomination</label>
                                    <input type="text" class="form-control" id="titleInput" name="title"
                                        placeholder="D&eacute;nomination" value="{{$company->title ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">T&eacute;l&eacute;phone</label>
                                    <input type="text" class="form-control" id="phone" name="ph" placeholder="Num&eacute;ro de t&eacute;l&eacute;phone" value="{{$company->phone ?? "" ?? ""}}">
                                    <input type="hidden" name="phone_number[mobile]">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="emailInput" class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" id="emailInput" placeholder="Votre adresse email" name="email" value="{{$company->email ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="addressInput" class="form-label">Adresse postale</label>
                                    <input type="text" class="form-control" id="addressInput" placeholder="Votre adresse postale" name="address" value="{{$company->address ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="mb-3 pb-2">
                                    <label for="description" class="form-label">Pr&eacute;sentation</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Votre pr&eacute;sentation" rows="3">{{$company->description ?? ""}}</textarea>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="mb-3 pb-2">
                                    <label for="physical_address" class="form-label">Localisation</label>
                                    <textarea class="form-control" id="physical_address" name="physical_address" placeholder="Votre situation g&eacute;graphique" rows="3">{{$company->physical_address ?? ""}}</textarea>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="mb-3 pb-2">
                                    <label for="timing" class="form-label">Horaire d'ouverture</label>
                                    <textarea class="form-control" id="timing" name="timing" placeholder="Vos horaires" rows="3">{{$company->timing ?? ""}}</textarea>
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
                    <div class="tab-pane" id="docDetails" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="rcInput" class="form-label">Registre de Commerce</label>
                                    <input type="text" class="form-control" id="rcInput" name="rc"
                                        placeholder="Registre de Commerce" value="{{$company->rc ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="ccInput" class="form-label">Compte Contribuable</label>
                                    <input type="text" class="form-control" id="ccInput" name="cc"
                                        placeholder="Compte Contribuable" value="{{$company->cc ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="cnpsInput" class="form-label">CNPS #</label>
                                    <input type="text" class="form-control" id="cnpsInput" name="cnps"
                                        placeholder="CNPS #" value="{{$company->cnps ?? ""}}">
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
                    <div class="tab-pane" id="socialDetails" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="facebook" class="form-label">Facebook</label>
                                    <input type="text" class="form-control" id="facebook" name="facebook" placeholder="https://facebook.com/Identifiant" value="{{$company->facebook ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="instragram" class="form-label">Instagram</label>
                                    <input type="text" class="form-control" id="instragram" name="instragram" placeholder="https://instragram.com/Identifiant" value="{{$company->instragram ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="twitter" class="form-label">Twitter</label>
                                    <input type="text" class="form-control" id="twitter" name="twitter" placeholder="https://twitter.com/Identifiant" value="{{$company->twitter ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="linkedin" class="form-label">Linkedin</label>
                                    <input type="text" class="form-control" id="linkedin" name="linkedin" placeholder="https://linkedin.com/in/identifiant" value="{{$company->linkedin ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="web" class="form-label">Site web</label>
                                    <input type="text" class="form-control" id="web" name="web" placeholder="https://site.com" value="{{$company->web ?? ""}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="submit" class="btn btn-success">Enregistrer</button>
                                    <button type="button" class="btn btn-soft-success">Cancel</button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end tab-pane-->
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
                hiddenInput: "phone",
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
