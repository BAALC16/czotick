@extends('public.layouts')
@section('title') LogIn ! @endsection
@section('content')
<section class="py-13">
    <div class="container">
      <div class="row">
        <div class="col-lg-7 mx-auto">
          <div class="card border-0 blockui shadow-xxs-2 login-register">
            <div class="card-body p-6">
              <h2 class="card-title fs-30 font-weight-600 text-dark lh-16 mb-2">Connexion</h2>
              <p class="mb-4">Vous n'avez pas de compte ? <a  href="/Register" class="text-heading hover-primary"><u>Inscrivez-vous.</u></a></p>
              <form class="form form-xhr" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                  <label for="email" class="text-heading">E-mail</label>
                  <input type="email" name="email"
                                   class="form-control form-control-lg border-0"
                                   id="email" placeholder="Votre adresse E-mail" required autofocus>
                </div>
                <div class="form-group">
                  <label for="password2" class="text-heading">Mot de passe</label>
                  <input type="password" class="form-control form-control-lg border-0"
                  id="password2" name="password" required
                  placeholder="Votre mot de passe">
                </div>
                <div class="form-check mb-4">
                  <input class="form-check-input" type="checkbox" value="" id="remember-me2" name="remember" value="1">
                  <label class="form-check-label" for="remember-me2">
                    Rester connecté
                  </label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg rounded">Connexion</button>
                <a href="{{ route('password.request') }}" class="d-block mt-3 ml-auto text-orange fs-15">
                  Mot de passe oublié ?
                </a>
              </form>
              
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
@endsection
