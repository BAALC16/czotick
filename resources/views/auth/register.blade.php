@extends('layouts.master-without-nav')
@section('title')
@lang('translation.signup')
@endsection
@section('content')

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="/" class="d-inline-block auth-logo">
                                    <img src="/images/logo-white.png" alt="" height="80">
                                </a>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Rejoignez la JCI COTE D'IVOIRE</h5>
                                    <p class="text-muted">Cr&eacute;ez votre compte d&egrave;s maintenant et acc&eacute;dez &agrave; toutes les fonctionnalit&eacute;s</p>
                                </div>
                                <div>
                                    <form class="needs-validation form-xhr blockui" method="POST"
                                        action="{{ route('register') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="useremail" class="form-label">Adresse e-mail <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}" id="useremail"
                                                placeholder="Votre adresse e-mail">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <div class="invalid-feedback">
                                                Entrez votre adresse e-mail
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nom" class="form-label">Nom <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                                name="nom" value="{{ old('nom') }}" id="nom"
                                                placeholder="Votre nom">
                                            @error('nom')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <div class="invalid-feedback">
                                                Entrez votre nom
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="prenoms" class="form-label">Pr&eacute;noms <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('prenoms') is-invalid @enderror"
                                                name="prenoms" value="{{ old('prenoms') }}" id="prenoms"
                                                placeholder="Votre(s) pr&eacute;nom(s)">
                                            @error('prenoms')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <div class="invalid-feedback">
                                                Entrez votre(s) pr&eacute;nom(s)
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label for="password" class="form-label">Mot de passe <span
                                                    class="text-danger">*</span></label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                id="password" placeholder="Votre mot de passe">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <div class="invalid-feedback">
                                                Entrez votre mot de passe
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Inscription</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="mt-4 text-center">
                            <p class="mb-0">Vous avez d&eacute;j&agrave; un compte ? <a href="{{ route('login') }}"
                                    class="fw-semibold text-primary text-decoration-underline"> Connexion </a> </p>
                        </div>

                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>document.write(new Date().getFullYear())</script> JCI CI <i class="mdi mdi-heart text-danger"></i> by AYIYIKOH
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/particles.js/particles.js.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/particles.app.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/form-validation.init.js') }}"></script>
@endsection
