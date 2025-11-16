@extends('layouts.master-without-nav')
@section('title')
  Réinitialisation du mot de passe
@endsection
@section('content')

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="/" class="d-block">
                                                    <img src="/images/logo-white.png" alt="" height="80">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>

                                                <div id="qoutescarouselIndicators" class="carousel slide"
                                                    data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="0" class="active" aria-current="true"
                                                            aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">"Dans un monde de plus en plus complexe, la JCI répond à l'un des besoins humains les plus élémentaires : le besoin d'amitié et de fraternité"</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"L'adhésion à une Organisation JCI contribue à développer de meilleurs citoyens de la communauté."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"La JCI développe la confiance et la compétence dans la communication publique et la possibilité de pratiquer et perfectionner ces compétences."</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end carousel -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <h5 class="text-primary">R&eacute;cup&eacute;ration du mot de passe</h5>
                                        <p class="text-muted">Vous avez oubli&eacute; votre mot de passe? No problem!</p>

                                        <div class="mt-2 text-center">
                                            <lord-icon src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop"
                                                colors="primary:#0ab39c" class="avatar-xl">
                                            </lord-icon>
                                        </div>

                                        <div class="alert alert-borderless alert-warning text-center mb-2 mx-2"
                                            role="alert">
                                            Entrez votre adresse email et les instructions vous seront envoy&eacute;es.
                                        </div>
                                        <div class="p-2">
                                            <form method="POST" action="{{ route('password.update') }}">
                                                @csrf
                                                <!-- Password Reset Token -->
                                                <input type="hidden" name="token" value="{{ $request->route('token') }}">


                                                <div class="mb-3">
                                                    <label for="username" class="form-label">E-mail</label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $request->email)}}" id="username" name="email" placeholder="Votre adresse e-mail">
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">

                                                    <label class="form-label" for="password-input">Nouveau Mot de passe</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 @error('password') is-invalid @enderror" name="password" placeholder="Votre mot de passe" id="password-input" value="">
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong> 
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="mb-3">

                                                    <label class="form-label" for="password-input1">Confirmez votre Mot de passe</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 @error('password') is-invalid @enderror" name="password_confirmation" placeholder="Votre mot de passe" id="password-input1" value="">
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                        @error('password_confirmation')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong> 
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="text-center mt-4">
                                                    <button class="btn btn-success w-100" type="submit">R&eacute;initialiser</button>
                                                </div>
                                            </form><!-- end form -->
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Vous vous &ecirc;tes souvenus du mot de passe? <a
                                                    href="{{ route('login') }}"
                                                    class="fw-semibold text-primary text-decoration-underline"> Connectez-vous
                                                </a> </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

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
    <script src="{{ URL::asset('assets/js/pages/password-addon.init.js') }}"></script>
@endsection