@if (!auth()->check())
  <div class="modal fade login-register login-register-modal" id="login-register-modal" tabindex="-1" role="dialog"
  aria-labelledby="login-register-modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mxw-571" role="document">
    <div class="modal-content blockui">
      <div class="modal-header border-0 p-0">
        <div class="nav nav-tabs row w-100 no-gutters" id="myTab" role="tablist">
          <a class="nav-item col-sm-8 ml-0 nav-link py-4 px-6 fs-18" id="register-tab" data-toggle="tab"
          href="#register"
          role="tab"
          aria-controls="register" aria-selected="false">Rejoignez la Communaut&eacute; MCK!</a>
          <div class="nav-item col-sm-4 ml-0 d-flex align-items-center justify-content-end">
            <button type="button" class="close m-0 fs-23" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
      </div>
      <div class="modal-body p-4 py-sm-7 px-sm-8">
        <div class="tab-content shadow-none p-0" id="myTabContent">
        <div class="tab-pane fade show active" id="register" role="tabpanel" aria-labelledby="register-tab">
          <form class="form form-xhr" action="{{ route('register') }}" method="post">
            <div class="form-group mb-4">
              <label for="full-name" class="sr-only">Nom</label>
              <div class="input-group input-group-lg">
                <div class="input-group-prepend ">
                  <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                    <i class="far fa-address-card"></i></span>
                  </div>
                  <input type="text" class="form-control border-0 shadow-none fs-13"
                  id="full-name" name="nom" required
                  placeholder="Nom de famille">
                </div>
              </div>
            <div class="form-group mb-4">
              <label for="given-name" class="sr-only">Prénom(s)</label>
              <div class="input-group input-group-lg">
                <div class="input-group-prepend ">
                  <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                    <i class="far fa-user"></i></span>
                  </div>
                  <input type="text" class="form-control border-0 shadow-none fs-13"
                  id="given-name" name="prenoms" required
                  placeholder="Prénom(s)">
                </div>
              </div>
              <div class="form-group mb-4">
                <label for="username01" class="sr-only">E-mail</label>
                <div class="input-group input-group-lg">
                  <div class="input-group-prepend ">
                    <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                      <i class="far fa-at"></i></span>
                    </div>
                    <input type="text" class="form-control border-0 shadow-none fs-13"
                    id="username01" name="email" required
                    placeholder="E-mail">
                  </div>
                </div>
                <div class="form-group mb-4">
                  <label for="password01" class="sr-only">Mot de passe</label>
                  <div class="input-group input-group-lg">
                    <div class="input-group-prepend ">
                      <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                        <i class="far fa-lock"></i>
                      </span>
                    </div>
                    <input type="password" class="form-control border-0 shadow-none fs-13"
                    id="password01" name="password" required
                    placeholder="Mot de passe">
                    <div class="input-group-append">
                      <span class="input-group-text bg-gray-01 border-0 text-body fs-18">
                        <i class="far fa-eye-slash toggle-password-visibility"></i>
                      </span>
                    </div>
                  </div>
                  <p class="form-text">Au moins 8 caractères dont au moins un chiffre et une lettre</p>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">Inscription</button>
                @csrf
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endif
