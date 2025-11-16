@extends('backoffice.layouts')
@section('title')
    {{ $user->full_name }}
@endsection
@section('content')
    <section class="bg-gray-01 pt-4 pb-13">
      <div class="container">
          <div class="row">
              <div class="col-lg-4 primary-sidebar sidebar-sticky" id="sidebar">
                  <div class="primary-sidebar-inner">
                        <div class="card p-6 mb-4">
                            <div class="card-body text-center p-0">
                                <img src="{{ $user->photo_url }}" alt="{{ $user->full_name }}"
                                    class="rounded-circle w-50 mb-2">
                                <p class="d-block fs-16 lh-214 text-dark mb-0 font-weight-500">{{ $user->full_name }}</p>
                                <p class="mb-0">{{ $user->titre ?? 'Utilisateur ' . config('app.name') }}</p>
                            </div>
                            <div class="card-footer bg-white px-0 pt-1">
                                <ul class="list-group list-group-no-border mb-7">
                                    <li class="list-group-item d-flex align-items-sm-center lh-114 row m-0 px-0 pt-3 pb-0">
                                        <span class="col-3 p-0 fs-13">Nom</span>
                                        <span
                                            class="col-9 p-0 text-heading font-weight-500">{{ $user->full_name }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-sm-center lh-114 row m-0 px-0 pt-3 pb-0">
                                        <span class="col-3 p-0 fs-13">Ville</span>
                                        <span
                                            class="col-9 p-0 text-heading font-weight-500">{{ $user->ville ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-sm-center lh-114 row m-0 px-0 pt-3 pb-0">
                                        <span class="col-3 p-0 fs-13">Portable</span>
                                        <span
                                            class="col-9 p-0 text-heading font-weight-500">{{ $user->mobile ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-sm-center lh-114 row m-0 px-0 pt-3 pb-0">
                                        <span class="col-3 p-0 fs-13">Fixe</span>
                                        <span
                                            class="col-9 p-0 text-heading font-weight-500">{{ $user->telephone ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-sm-center row m-0 px-0 pt-2 pb-0">
                                        <span class="col-3 p-0 fs-13">E-mail</span>
                                        <span class="col-9 p-0">{{ $user->email }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-sm-center row m-0 px-0 pt-2 pb-0">
                                        <span class="col-3 p-0 fs-13">Site web</span>
                                        <span class="col-9 p-0">{{ $user->site_web ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-sm-center lh-114 row m-0 px-0 pt-3 pb-0">
                                        <span class="col-3 p-0 fs-13">Liens Sociaux</span>
                                        <ul class="col-9 list-inline text-gray-lighter m-0 p-0 z-index-2">
                                            <li class="list-inline-item m-0">
                                                <a href="{{ $user->twitter ?? '#' }}"
                                                    class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                        class="fab fa-twitter"></i></a>
                                            </li>
                                            <li class="list-inline-item mr-0 ml-2">
                                                <a href="{{ $user->facebook ?? '#' }}"
                                                    class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                        class="fab fa-facebook-f"></i></a>
                                            </li>
                                            <li class="list-inline-item mr-0 ml-2">
                                                <a href="{{ $user->instagram ?? '#' }}"
                                                    class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                        class="fab fa-instagram"></i></a>
                                            </li>
                                            <li class="list-inline-item mr-0 ml-2">
                                                <a href="{{ $user->linkedin ?? '#' }}"
                                                    class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                        class="fab fa-linkedin-in"></i></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                                <button type="button" data-toggle="modal" data-target="#modal-messenger" class="btn btn-primary btn-lg btn-block shadow-none">Envoyer un
                                    message
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mb-6 mb-lg-0">
                    <h2 class="fs-22 text-heading lh-15 mb-6">&Agrave; Propos de {{ $user->prenoms }}</h2>
                    <div class="card border-0 mb-10">
                        <div class="card-body py-5 px-6">
                            <h3 class="card-title text-heading fs-16 lh-213">
                                Présentation
                            </h3>
                            <p class="lh-214 mb-6">
                                @if ($user->introduction)
                                    {{ $user->introduction }}
                                @else
                                    <em>{{ $user->prenoms }} n'a pas encore rempli son texte de présentation.</em>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="collapse-tabs mb-10">
                        <ul class="nav nav-tabs text-uppercase d-none d-md-inline-flex agent-details-tabs" role="tablist">
                            <li class="nav-item">
                                <a href="#roles" class="nav-link active shadow-none fs-13" data-toggle="tab" role="tab">
                                    Rôles ({{ $user->roles->count() }})
                                </a>
                            </li>
                            <li class="nav-item ml-0">
                                <a href="#demandes" class="nav-link shadow-none fs-13" data-toggle="tab" role="tab">
                                    Demandes de services
                                </a>
                            </li>
                            <li class="nav-item ml-0">
                                <a href="#rent" class="nav-link shadow-none fs-13" data-toggle="tab" role="tab">
                                    Autre
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content shadow-none pt-7 pb-0 px-6 bg-white">
                            <div id="collapse-tabs-accordion-01">
                                <div class="tab-pane tab-pane-parent fade show active" id="roles" role="tabpanel">
                                    <div class="card border-0 bg-transparent">
                                        <div class="card-header border-0 d-block d-md-none bg-transparent px-0 py-1"
                                            id="headingAll-01">
                                            <h5 class="mb-0">
                                                <button
                                                    class="btn lh-2 fs-18 bg-white py-1 px-6 mb-4 shadow-none w-100 collapse-parent border"
                                                    data-toggle="collapse" data-target="#all-collapse-01"
                                                    aria-expanded="true" aria-controls="all-collapse-01">
                                                    Rôles ({{ $user->roles->count() }})
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="all-collapse-01" class="collapse show collapsible"
                                            aria-labelledby="headingAll-01" data-parent="#collapse-tabs-accordion-01">
                                            <div class="card-body p-0 pb-3">
                                                <ul class="list-group list-group-no-border">
                                                    @forelse($user->roles as $r)
                                                        <li class="list-group-item p-0">
                                                            <span class="@if (!$r->pivot->active) text-danger @else text-primary @endif d-inline-block mr-2"><i
                                                                    class="{{ $r->icone ?? 'far fa-users' }}"></i></span>
                                                            <span class="lh-26">{{ $r->nom }} 
                                                                @can('view', $r)
                                                                    <a
                                                                    href="{{ route('roles.show', $r) }}" data-toggle="tooltip" title="Voir le rôle"
                                                                    target="_blank"><span
                                                                        class="far fa-external-link-square fs-13"></span></a>
                                                                @endcan
                                                            </span>
                                                            @can('view', $r)
                                                                <small class="text-muted">
                                                                    Assigné le
                                                                    @if ($r->pivot->granted_at)
                                                                        {{ Carbon\Carbon::parse($r->pivot->granted_at)->isoFormat('DD MMM Y à HH:mm') }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                    @if ($r->pivot->granter_id)
                                                                        @php
                                                                            $granter = App\Models\User::find($r->pivot->granter_id);
                                                                        @endphp
                                                                        par {{ $granter->prenoms }}
                                                                    @endif
                                                                </small>
                                                            @endcan
                                                        </li>
                                                    @empty
                                                        <em>Aucun rôle.</em>
                                                    @endforelse
                                                    @can('assign', App\Models\Role::class)
                                                        <div class="py-4">
                                                            <button class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                                                data-target="#modal-add-roles">Modifier les rôles</button>

                                                            {{-- Modal attach new roles --}}
                                                            <div class="modal fade" id="modal-add-roles"
                                                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                                                role="dialog" aria-labelledby="staticBackdropLabel2"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="staticBackdropLabel2">Modifier les rôles
                                                                            </h5>
                                                                            <button type="button" class="close"
                                                                                data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body blockui">
                                                                            <form method="post" id="form_sync_roles"
                                                                                class="form-xhr"
                                                                                action="{{ route('users.sync-roles', $user) }}">
                                                                                @csrf
                                                                                <input type="hidden" name="_method"
                                                                                    value="PATCH" />
                                                                                <div class="row">
                                                                                    <div class="col-lg-2"></div>
                                                                                    <div class="col-lg-8">
                                                                                        @foreach($roles as $r)
                                                                                        <div class="custom-control custom-switch">
                                                                                            <input @if($user->roles->contains($r->id)) checked @endif name="roles[]" value="{{$r->id}}" type="checkbox" class="custom-control-input" id="customSwitch{{$r->id}}">
                                                                                            <label class="custom-control-label fs-16" for="customSwitch{{$r->id}}">{{$r->nom}}</label>
                                                                                        </div>
                                                                                        @endforeach
                                                                                        <div class="text-center mt-4 py-3">
                                                                                            <button type="submit"
                                                                                                class="btn btn-lg btn-primary">Enregistrer</button>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2"></div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                        
                                                    @endcan
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane tab-pane-parent fade" id="sale" role="tabpanel">
                                    <div class="card border-0 bg-transparent">
                                        <div class="card-header border-0 d-block d-md-none bg-transparent p-0"
                                            id="headingSale-01">
                                            <h5 class="mb-0">
                                                <button
                                                    class="btn lh-2 fs-18 bg-white py-1 px-6 shadow-none w-100 collapse-parent border collapsed mb-4"
                                                    data-toggle="collapse" data-target="#sale-collapse-01"
                                                    aria-expanded="true" aria-controls="sale-collapse-01">
                                                    For Sale (5)
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="sale-collapse-01" class="collapse collapsible"
                                            aria-labelledby="headingSale-01" data-parent="#collapse-tabs-accordion-01">
                                            <div class="card-body p-0">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane tab-pane-parent fade" id="rent" role="tabpanel">
                                    <div class="card border-0 bg-transparent">
                                        <div class="card-header border-0 d-block d-md-none bg-transparent p-0"
                                            id="headingRent-01">
                                            <h5 class="mb-0">
                                                <button
                                                    class="btn lh-2 fs-18 bg-white py-1 px-6 shadow-none w-100 collapse-parent border collapsed mb-4"
                                                    data-toggle="collapse" data-target="#rent-collapse-01"
                                                    aria-expanded="true" aria-controls="rent-collapse-01">
                                                    For Rent (3)
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="rent-collapse-01" class="collapse collapsible"
                                            aria-labelledby="headingRent-01" data-parent="#collapse-tabs-accordion-01">
                                            <div class="card-body p-0">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <x-new-message-popup :user="$user" />
@endsection
