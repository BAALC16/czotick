@extends('layouts.master')
@section('title') Créer un Administrateur @endsection
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
                <h3 class="text-white mb-1"> Créer un Administrateur </h3>
            </div>
        </div>

    </div>
</div>

<form method="post" action="{{route('users.store')}}" enctype="multipart/form-data" class="form-xhr">
    @csrf
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
        <div class="row g-4">
            <div class="col mb-4">


            </div>

        </div>
    </div>
    <div class="row">

        <!--end col-->
        <div class="col-xxl-9">
            <div class="card mt-xxl-n5">
                <div class="card-body p-4">
                    <div id="personalDetails" role="tabpanel">
                        <div class="row">
                                <!--end col-->
                                <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="lastnameInput" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="lastnameInput" name="nom"
                                        placeholder="Votre nom" value="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="firstnameInput" class="form-label">Pr&eacute;noms</label>
                                    <input type="text" class="form-control" id="firstnameInput" name="prenoms"
                                        placeholder="Vos pr&eacute;noms" value="">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="emailInput" class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" id="emailInput" placeholder="Votre adresse email" name="email" value="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="countryInput" class="form-label">Rôle</label>
                                    <select class="form-control" name="role" id="role">
                                        <option value="2">Administrateur</option>
                                        <option value="4">Redacteur</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="newpasswordInput" class="form-label">Mot de passe*</label>
                                    <input type="password" class="form-control" id="newpasswordInput" name="password">
                                </div>
                            </div>
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
