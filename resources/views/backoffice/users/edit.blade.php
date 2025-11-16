@extends('backoffice.layouts')
@section('title')
    Modifier les infos - {{ $user->full_name }}
@endsection
@section('content')
    <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10 blockui">
        <div class="mb-6">
            <h2 class="mb-0 text-heading fs-22 lh-15">{{$user->full_name}}
            </h2>
            <p class="mb-1">Modifier les informations de compte</p>
        </div>
        <form method="post" action="{{route('users.update', $user)}}" enctype="multipart/form-data" class="form-xhr">
            @csrf
            <input type="hidden" name="_method" value="PATCH" />
            <div class="row mb-6">
                <div class="col-lg-6">
                    <div class="card mb-6">
                        <div class="card-body px-6 pt-6 pb-5">
                            <div class="row">
                                <div class="col-sm-4 col-xl-12 col-xxl-7 mb-6">
                                    <h3 class="card-title mb-0 text-heading fs-22 lh-15">Photo</h3>
                                    <p class="card-text">Téléverser une photo de profil</p>
                                </div>
                                <div class="col-sm-8 col-xl-12 col-xxl-5">
                                    <img src="{{$user->photo_url}}" alt="My Profile" class="w-100" />
                                    <div class="custom-file mt-4 h-auto">
                                        <input type="file" class="custom-file-input" hidden id="customFile" name="photo_file" accept="image/*">
                                        <label class="btn btn-secondary btn-lg btn-block" for="customFile">
                                            <span class="d-inline-block mr-1"><i
                                                    class="fal fa-cloud-upload"></i></span>Nouvelle photo</label>
                                    </div>
                                    <p class="mb-0 mt-2">
                                        *minimum 500px x 500px
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-6">
                        <div class="card-body px-6 pt-6 pb-5">
                            <h3 class="card-title mb-0 text-heading fs-22 lh-15">Informations de contact</h3>
                            <div class="form-row mx-n4">
                                <div class="form-group col-md-6 px-4">
                                    <label for="firstName" class="text-heading">Prénoms</label>
                                    <input type="text" class="form-control form-control-lg border-0" id="firstName"
                                        name="prenoms" value="{{$user->prenoms}}">
                                </div>
                                <div class="form-group col-md-6 px-4">
                                    <label for="lastName" class="text-heading">Nom</label>
                                    <input type="text" class="form-control form-control-lg border-0" id="lastName"
                                        name="nom" value="{{$user->nom}}">
                                </div>
                            </div>
                            <div class="form-row mx-n4">
                                <div class="form-group col-md-6 px-4">
                                    <label for="mobile" class="text-heading">Portable</label>
                                    <input type="text" class="form-control form-control-lg border-0" id="mobile"
                                        name="mobile" value="{{$user->mobile}}">
                                </div>
                                <div class="form-group col-md-6 px-4">
                                    <label for="phone" class="text-heading">Fixe</label>
                                    <input type="text" class="form-control form-control-lg border-0" id="phone"
                                        name="telephone" value="{{$user->telephone}}">
                                </div>
                            </div>
                            <div class="form-row mx-n4">
                                <div class="form-group col-md-6 px-4 mb-md-0">
                                    <label for="email" class="text-heading">E-mail</label>
                                    <input type="email" class="form-control form-control-lg border-0" id="email"
                                        name="email" value="{{$user->email}}">
                                </div>
                                <div class="form-group col-md-6 px-4 mb-md-0">
                                    <label for="skype" class="text-heading">E-mail Pro</label>
                                    <input type="text" class="form-control form-control-lg border-0" id="skype"
                                        name="skype">
                                </div>
                            </div>
                            <div class="form-row mx-n4">
                              <div class="form-group col-md-6 px-4 mb-md-0">
                                  <label for="ville" class="text-heading">Ville</label>
                                  <input type="ville" class="form-control form-control-lg border-0" id="ville"
                                      name="ville" value="{{$user->ville}}">
                              </div>
                              <div class="form-group col-md-6 px-4 mb-md-0">
                                  <label for="skype" class="text-heading">Pays</label>
                                  <select class="form-control form-select form-control-lg border-0" id="pays"
                                      name="pays">
                                      @foreach(App\Models\Country::all()->sortBy('nom') as $p)
                                        <option value="{{$p->code}}" @if($p->code === $user->code_pays) selected @endif>{{$p->nom}}</option>
                                      @endforeach
                                  </select>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-6 mb-lg-0">
                        <div class="card-body px-6 pt-6 pb-5">
                            <h3 class="card-title mb-0 text-heading fs-22 lh-15">Titre du compte</h3>
                            <p class="card-text">Le titre peut être votre profession ou votre rôle dans le système</p>
                            <div class="form-group mb-0">
                                <label for="title" class="text-heading">Titre / Poste</label>
                                <input type="text" class="form-control form-control-lg border-0" id="title" name="titre" value="{{$user->titre}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card mb-6">
                        <div class="card-body px-6 pt-6 pb-5">
                            <h3 class="card-title mb-0 text-heading fs-22 lh-15">Social</h3>
                            <p class="card-text">Liens vers des comptes de réseaux sociaux</p>
                            <div class="form-group">
                                <label for="facebook" class="text-heading">Facebook</label>
                                <input type="url" class="form-control form-control-lg border-0" id="facebook"
                                    name="facebook" placeholder="https://facebook.com/Identifiant" value="{{$user->facebook}}">
                            </div>
                            <div class="form-group">
                                <label for="instagram" class="text-heading">Instagram</label>
                                <input type="url" class="form-control form-control-lg border-0" id="instagram"
                                    name="instagram" placeholder="https://instagram.com/Identifiant" value="{{$user->instagram}}">
                            </div>
                            <div class="form-group">
                                <label for="twitter" class="text-heading">Twitter</label>
                                <input type="url" class="form-control form-control-lg border-0" id="twitter" name="twitter" placeholder="https://twitter.com/Identifiant" value="{{$user->twitter}}">
                            </div>
                            <div class="form-group">
                                <label for="linkedin" class="text-heading">Linkedin</label>
                                <input type="url" class="form-control form-control-lg border-0" id="linkedin"
                                    name="linkedin" placeholder="https://linkedin.com/in/identifiant" value="{{$user->linkedin}}">
                            </div>
                            <div class="form-group mb-7">
                                <label for="website" class="text-heading">Site web<span
                                        class="text-muted">(avec http ou https)</span>
                                </label>
                                <input type="url" class="form-control form-control-lg border-0" id="website" name="site_web" placeholder="https://siteweb.com" value="{{$user->site_web}}">
                            </div>
                            <div class="form-group mb-7">
                                <label for="introduction" class="text-heading">Présentation</label>
                                <textarea class="form-control form-control-lg border-0" id="introduction" name="introduction">{{$user->introduction}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body px-6 pt-6 pb-5">
                            <h3 class="card-title mb-0 text-heading fs-22 lh-15">Mot de passe</h3>
                            <p class="card-text">Changer de mot de passe</p>
                            @if($user->is(auth()->user()))
                                <div class="form-group">
                                    <label for="oldPassword" class="text-heading">Mot de passe actuel</label>
                                    <input type="password" class="form-control form-control-lg border-0" id="oldPassword"
                                        name="old_password">
                                </div>
                            @endif
                            <div class="form-row mx-n4">
                                <div class="form-group col-md-6 col-lg-12 col-xxl-6 px-4">
                                    <label for="newPassword" class="text-heading">Nouveau mot de passe</label>
                                    <input type="password" class="form-control form-control-lg border-0" id="newPassword"
                                        name="new_password">
                                </div>
                                <div class="form-group col-md-6 col-lg-12 col-xxl-6 px-4">
                                    <label for="confirmNewPassword" class="text-heading">Confirmation du nouveau mdp</label>
                                    <input type="password" class="form-control form-control-lg border-0"
                                        id="confirmNewPassword" name="new_password_confirmation">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end flex-wrap">
                <button type="button" class="btn btn-lg bg-hover-white border rounded-lg mb-3">Supprimer le compte</button>
                <button type="submit" class="btn btn-lg btn-primary ml-4 mb-3">Enregistrer</button>
            </div>
        </form>
    </div>
@endsection
