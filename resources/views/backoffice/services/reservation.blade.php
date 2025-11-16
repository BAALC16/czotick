@extends('backoffice.layouts')
@section('title')
    Demande de Service #{{ $reservation->id }}
@endsection
@section('content')
    <section class="bg-gray-01 pt-4 pb-13">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 primary-sidebar sidebar-sticky" id="sidebar">
                    <div class="primary-sidebar-inner">
                        <div class="card p-6 mb-4">
                            <div class="card-body text-center p-0">
                                <img width="140" src="{{ $reservation->user->photo_url }}"
                                    alt="{{ $reservation->user->full_name }}" class="mb-2 rounded-circle">
                                <p class="d-block fs-16 lh-214 text-dark mb-0 font-weight-500">
                                    @can('view', $reservation->user)
                                        <a href="{{route('users.show', $reservation->user)}}" class="text-dark">
                                            {{ $reservation->user->full_name }}
                                        </a>
                                    @else
                                        {{ $reservation->user->full_name }}
                                    @endcan
                                </p>
                                <p class="mb-0">
                                    {{ $reservation->user->titre ?? 'Utilisateur ' . config('app.name') }}</p>
                            </div>
                            <div class="card-footer bg-white px-0 pt-1">
                                <ul class="list-group list-group-no-border mb-7">
                                    <li class="list-group-item d-flex align-items-sm-center lh-114 row m-0 px-0 pt-3 pb-0">
                                        <span class="col-3 p-0 fs-13">Mobile</span>
                                        <a href="tel:{{ $reservation->user->mobile }}">
                                            <span
                                                class="col-9 p-0 text-heading font-weight-500">{{ $reservation->user->mobile }}</span>
                                        </a>
                                    </li>
                                    @if ($reservation->user->telephone)
                                        <li
                                            class="list-group-item d-flex align-items-sm-center lh-114 row m-0 px-0 pt-3 pb-0">
                                            <span class="col-3 p-0 fs-13">Téléphone</span>
                                            <a href="tel:{{ $reservation->user->telephone }}">
                                                <span
                                                    class="col-9 p-0 text-heading font-weight-500">{{ $reservation->user->telephone }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="list-group-item d-flex align-items-sm-center row m-0 px-0 pt-2 pb-0">
                                        <span class="col-3 p-0 fs-13">E-mail</span>
                                        <a href="mailto:{{ $reservation->user->email }}">
                                            <span class="col-9 p-0">{{ $reservation->user->email }}</span>
                                        </a>
                                    </li>
                                    <li class="list-group-item d-flex align-items-sm-center lh-114 row m-0 px-0 pt-3 pb-0">
                                        <span class="col-3 p-0 fs-13">Social</span>
                                        <ul class="col-9 list-inline text-gray-lighter m-0 p-0 z-index-2">
                                            <li class="list-inline-item m-0">
                                                <a href="{{$reservation->user->twitter ?? '#'}}"
                                                    class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                        class="fab fa-twitter"></i></a>
                                            </li>
                                            <li class="list-inline-item mr-0 ml-2">
                                                <a href="{{$reservation->user->facebook ?? '#'}}"
                                                    class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                        class="fab fa-facebook-f"></i></a>
                                            </li>
                                            <li class="list-inline-item mr-0 ml-2">
                                                <a href="{{$reservation->user->instagram ?? '#'}}"
                                                    class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                        class="fab fa-instagram"></i></a>
                                            </li>
                                            <li class="list-inline-item mr-0 ml-2">
                                                <a href="{{$reservation->user->linkedin ?? '#'}}"
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
                    <h2 class="fs-22 text-heading lh-15 mb-6">Vue d'ensemble</h2>
                    <div class="card border-0 mb-10">
                        <div class="card-body py-5 px-6">
                            <h3 class="card-title text-heading fs-16 lh-213">
                                Détails de la demande
                            </h3>
                            <div class="lh-214 mb-6">
                                <ul class="list-group list-group-no-border mb-4">
                                    <li
                                        class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                        <span class="col-sm-3 px-1">Date </span>
                                        <span class="col-sm-9 px-1 text-heading font-weight-500">
                                            {{ Carbon\Carbon::parse($reservation->created_at)->isoFormat('dddd DD MMMM YYYY, HH:mm') }}
                                        </span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                        <span class="col-sm-3 px-1">Service </span>
                                        <span class="col-sm-9 px-1 text-heading font-weight-500"> 
                                            @can('view', $reservation->service)
                                                <a
                                                    href="{{ route('services.show', $reservation->service) }}">{{ $reservation->service->label }}</a>
                                            @else
                                                {{ $reservation->service->label }}
                                            @endcan
                                        </span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                        <span class="col-sm-3 px-1">Utilisateur </span>
                                        <span class="col-sm-9 px-1 text-heading font-weight-500"> 
                                            @can('view', $reservation->user)
                                                <a
                                                    href="{{route('users.show', $reservation->user)}}">{{ $reservation->user->full_name }}</a>
                                            @else
                                                {{ $reservation->user->full_name }}
                                            @endcan
                                             </span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                        <span class="col-sm-3 px-1">Prestataire </span>
                                        <span class="col-sm-9 px-1 text-heading font-weight-500"> 
                                            @if($reservation->prestataire)
                                                @can('view', $reservation->prestataire)
                                                    <a
                                                        href="{{route('users.show', $reservation->prestataire)}}">{{ $reservation->prestataire->full_name }}</a>
                                                @else
                                                    {{ $reservation->prestataire->full_name }}
                                                @endcan
                                            @else
                                                <em>Pas encore assignée</em>
                                            @endif
                                        </span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                        <span class="col-sm-3 px-1">Prix (FCFA)</span>
                                        <span class="col-sm-9 px-1 text-heading font-weight-500">
                                            {{ $reservation->prix ?? 'Non spécifié' }} </span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                        <span class="col-sm-3 px-1">Statut </span>
                                        <span class="col-sm-9 px-1 text-heading font-weight-500"> <span
                                                class="badge font-weight-normal fs-12 badge-{{ $reservation->status->color }}">{{ $reservation->status->label }}</span>
                                                @if($reservation->status_code == 'DEM_REJETEE')
                                                  <small class="btn btn-link btn-sm" data-toggle="tooltip" data-trigger="click" title="{{$reservation->justificatif ?? 'Aucun motif.'}}">Découvrir le motif</small>
                                                @endif
                                        </span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                        <span class="col-sm-3 px-1">Note </span>
                                        <p class="col-sm-9 px-1 font-weight-500 text-muted">
                                            {{ $reservation->note ?? 'Aucune note' }} </p>
                                    </li>
                                    @foreach ($reservation->service->attributs as $attr)
                                        <li
                                            class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mt-2 mt-sm-0 mb-2 mb-sm-0">
                                            <span class="col-sm-3 px-1">
                                                {{ $attr->label }}
                                                @if (!empty($attr->description))
                                                    <i class="fas fa-exclamation-circle ml-1 fs-2 text-muted"
                                                        data-toggle="tooltip" title="{{ $attr->description }}"></i>
                                                @endif
                                            </span>
                                            @if ($attr->type_champ == 'text' || $attr->type_champ == 'textarea')
                                                <span class="col-sm-9 px-1 lh-1 @if ($attr->type_champ == 'textarea') text-muted @else text-heading @endif font-weight-500">
                                                    {{ $reservation->attributs->where('id', $attr->id)->first()->pivot->valeur ?? 'Aucune valeur' }}
                                                </span>
                                            @else
                                                <ul class="list-group list-group-no-border">
                                                    @forelse ($reservation->attributs->where('id', $attr->id) as $file)
                                                        @php
                                                            $arr = json_decode($file->pivot->valeur, true);
                                                        @endphp
                                                        @continue(null === $arr)
                                                        <li class="list-group-item p-0">
                                                            <a href="{{ route('download', ['file' => $arr['chemin'], 'name' => strip_tags($arr['nom'])]) }}"
                                                                target="_blank">
                                                                <small class="font-weight-bolder">
                                                                    <span class="text-primary d-inline-block mr-1"><i
                                                                            class="far fa-download"></i></span>
                                                                    <span class="lh-26">{{ $arr['nom'] }}
                                                                        ({{ number_format(Storage::disk('public')->size($arr['chemin']) / pow(2, 10), 2, ',', ' ') }}
                                                                        Ko)</span>
                                                                </small>
                                                            </a>
                                                        </li>
                                                    @empty
                                                        <em>Fichier non soumis</em>
                                                    @endforelse
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @if($reservation->devis)
                                <div id="accordion-style-02" class="accordion accordion-02">
                                    <div class="card border-top-0 border-right-0 border-left-0 border-bottom rounded-0 pb-5 mb-4 pl-7 pr-5 position-relative">
                                        <div class="card-header border-0 bg-white p-0" id="headingFour">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link fs-22 font-weight-500 p-0 border-0" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                    Devis
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion-style-02">
                                            <div class="card-body px-0 pt-2 pb-1 lh-214">
                                                <div class="lh-214 mb-6">
                                                    <ul class="list-group list-group-no-border mb-4">
                                                        <li
                                                            class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                            <span class="col-sm-3 px-1">Tarif (FCFA)</span>
                                                            <span class="col-sm-9 px-1 text-heading font-weight-500">
                                                                {{ $reservation->devis->cout }}
                                                            </span>
                                                        </li>
                                                        <li
                                                            class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                            <span class="col-sm-3 px-1">Description </span>
                                                            <div class="col-sm-9 px-1 font-weight-500 text-muted">
                                                                {!! $reservation->devis->description ?? 'Pas de description' !!} </div>
                                                        </li>
                                                        <li
                                                            class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                            <span class="col-sm-3 px-1">Date de début d'exécution</span>
                                                            <span class="col-sm-9 px-1 text-heading font-weight-500">
                                                                {{ Carbon\Carbon::parse($reservation->devis->debut_execution)->isoFormat('dddd DD MMMM YYYY') }}
                                                            </span>
                                                        </li>
                                                        <li
                                                            class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                            <span class="col-sm-3 px-1">Date de fin d'exécution</span>
                                                            <span class="col-sm-9 px-1 text-heading font-weight-500">
                                                                {{ $reservation->devis->fin_execution ? Carbon\Carbon::parse($reservation->devis->fin_execution)->isoFormat('dddd DD MMMM YYYY') : 'Non spécifiée' }}
                                                            </span>
                                                        </li>
                                                        <li
                                                            class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                            <span class="col-sm-3 px-1">Créé par</span>
                                                            <span class="col-sm-9 px-1 text-heading font-weight-500">
                                                                @can('view', $reservation->devis->user)
                                                                    <a href="{{ route('users.show', $reservation->devis->user) }}">{{$reservation->devis->user->full_name}}</a>
                                                                @else
                                                                    {{$reservation->devis->user->full_name}}
                                                                @endif
                                                            </span>
                                                        </li>
                                                        <li
                                                            class="list-group-item d-flex align-items-sm-center lh-214 row mx-n1 p-0 mb-2 mb-sm-0">
                                                            <span class="col-sm-3 px-1">Date de création</span>
                                                            <span class="col-sm-9 px-1 text-heading font-weight-500">
                                                                {{ Carbon\Carbon::parse($reservation->devis->created_at)->isoFormat('dddd DD MMMM YYYY, HH:mm') }}
                                                            </span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- <div class="mt-4 border-bottom"></div> --}}
                        </div>
                        <div class="card-footer card-action">
                            <div class="mt-4">
                                <div class="text-center">
                                    @switch($reservation->status_code)
                                        @case("DEM_SOUMISE")
                                            @can('validate', App\Models\Reservation::class)
                                                <button
                                                    data-href="{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'accepter']) }}"
                                                    data-toggle="modal" data-target="#modal_accept_reser" class="btn btn-success border fs-14 px-3 mr-3"
                                                    data-method="PATCH">Accepter
                                                    <span class="d-inline-block mr-1"><i
                                                            class="fas fa-check-circle"></i></span></button>
                                            @endcan
                                            @can('reject', App\Models\Reservation::class)
                                                <button
                                                    data-href="{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'rejeter']) }}"
                                                    data-toggle="modal" data-target="#modal_reject_reser" class="btn btn-warning border fs-14 px-3 mr-3" data-method="PATCH">Rejeter
                                                    <span class="d-inline-block ml-1"><i
                                                            class="fas fa-times-circle"></i></span></button>
                                            @endcan
                                        @break

                                        @case("DEM_ACCEPTEE")
                                            @if($reservation->prestataire_id == auth()->id())
                                                @if(auth()->user()->hasPermission('devis.create'))
                                                    <a href="{{route('reservations.devis.create', $reservation)}}"
                                                    class="btn border btn-primary fs-14 px-3 mr-3">
                                                        @if($reservation->devis) 
                                                            Modifier Devis
                                                        @else
                                                            Créer Devis
                                                        @endif
                                                        <span class="d-inline-block ml-1"><i
                                                                class="fas fa-file-invoice"></i></span></a>
                                                @endif
                                            @endif
                                            @can('assign', App\Models\Reservation::class)
                                                <button
                                                data-href="{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'accepter']) }}"
                                                data-toggle="modal" data-target="#modal_accept_reser" class="btn btn-success border fs-14 px-3 mr-3"
                                                data-method="PATCH">Assigner à un prestataire
                                                <span class="d-inline-block mr-1"><i
                                                        class="fas fa-user-cog"></i></span></button>
                                            @endcan
                                            @can('reject', App\Models\Reservation::class)
                                                <button
                                                    data-href="{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'rejeter']) }}"
                                                    data-toggle="modal" data-target="#modal_reject_reser" class="btn btn-warning border fs-14 px-3 mr-3" data-method="PATCH">Rejeter
                                                    <span class="d-inline-block ml-1"><i
                                                            class="fas fa-times-circle"></i></span></button>
                                            @endcan
                                            @can('validate', App\Models\Reservation::class)
                                                <button
                                                data-href="{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'traiter']) }}"
                                                class="switcher btn btn-primary border fs-14 px-3 mr-3" data-method="PATCH">Terminer
                                                <span class="d-inline-block ml-1"><i
                                                        class="fas fa-check-double"></i></span></button>
                                            @endcan
                                        @break

                                        @case("DEM_REJETEE")
                                            @can('validate', App\Models\Reservation::class)
                                                <button
                                                    data-href="{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'reprendre']) }}"
                                                    class="switcher btn btn-info border fs-14 px-3 mr-3"
                                                    data-method="PATCH">Reprendre
                                                    <span class="d-inline-block ml-1"><i class="fas fa-history"></i></span></button>
                                            @endcan
                                        @break

                                        @case("DEM_TRAITEE")
                                            @can('validate', App\Models\Reservation::class)
                                                <button
                                                    data-href="{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'non-traitee']) }}"
                                                    class="switcher btn btn-primary border fs-14 px-3 mr-3"
                                                    data-method="PATCH">Marquer comme non terminée
                                                    <span class="d-inline-block ml-1"><i class="fas fa-history"></i></span></button>
                                            @endcan
                                        @break
                                    @endswitch
                                    @can('update', $reservation)
                                        <a href="javascript:;"
                                        data-toggle="modal" data-target="#modal_update_reser" class="btn border fs-14 px-3 mr-3" title="Modifier"><span class="d-inline-block text-primary"><i
                                                    class="fas fa-pen-alt"></i></span></a>
                                    @endcan
                                    @can('delete', $reservation)
                                        <button data-href="{{ route('reservations.destroy', $reservation) }}"
                                            class="switcher btn btn-danger border fs-14 px-3 mr-3 confirm"
                                            data-confirm="Souhaitez-vous vraiment supprimer cette Demande ?"
                                            data-method="DELETE" title="Supprimer"><span class="d-inline-block"><i class="fas fa-trash-alt"></i></span></button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <h2 class="fs-22 text-heading lh-15 mb-6" id="comments">Messages</h2>
                    <div class="card border-0 mb-10">
                        <div class="card-body py-5 px-6">
                            @foreach ($reservation->comments as $k => $com)
                                <div class="media mb-6 pb-5 border-bottom">
                                    <div class="media-body">
                                        <p class="text-heading fs-16 font-weight-500 pb-0 lh-1 mb-0">
                                            @can('view', $com->user)
                                                <a href="{{route('users.show', $com->user)}}">{{ $com->user->full_name }}</a>
                                            @else
                                                {{ $com->user->full_name }}
                                            @endcan
                                        </p>
                                        <small class="text-muted">{{$com->user->titre ?? "Utilisateur ".config('app.name')}}</small>
                                        <p class="mb-4">
                                            @if($com->texte)
                                                {!! nl2br(strip_tags($com->texte)) !!}
                                            @endif
                                        </p>
                                        @if($com->pieces_jointes->isNotEmpty())
                                            <div class="pb-3">
                                                @foreach ($com->pieces_jointes as $pj)
                                                    <a href="{{ route('download', ['file' => $pj->chemin, 'name' => strip_tags($pj->nom)]) }}"
                                                        target="_blank" class="badge badge-accent bagde-lg font-weight-lighter text-lowercase">
                                                        <span class="text-primary d-inline-block mr-1"><i
                                                                class="far fa-download"></i></span>
                                                        <span class="">{{ $pj->nom }}
                                                            ({{ number_format(Storage::disk('public')->size($pj->chemin) / pow(2, 10), 2, ',', ' ') }}
                                                            Ko)</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        <ul class="list-inline">
                                            <li class="list-inline-item text-muted">{{Carbon\Carbon::parse($com->created_at)->isoFormat("DD MMM YYYY \à HH:mm")}}<span
                                                class="d-inline-block ml-2 mr-2">|</span> {{($k+1).' sur '.$reservation->comments->count()}}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach                            
                             <div class="card border-0">
                                <div class="card-body p-0">
                                    <h3 class="fs-16 lh-2 text-heading mb-4">Ecrire un message</h3>
                                    <form method="post" class="form-xhr blockui" action="{{route('reservations.comment', $reservation)}}" enctype="multipart/form-data">
                                        <div class="form-group mb-3">
                                            <textarea class="form-control form-control-lg border-0" placeholder="Entrez le message"
                                                name="texte" rows="5"></textarea>
                                        </div>
                                        <input type="file" name="fichiers[]" class="d-none" id="comment-files" multiple />
                                        <button onclick="document.getElementById('comment-files').click();" class="btn btn-link d-block mb-4" type="button" id="btn-comment-files">Joindre des fichiers</button>
                                        <button type="submit" class="btn btn-lg btn-primary px-10 mb-2">Envoyer</button>
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @can('validate', App\Models\Reservation::class)
        {{-- Modal accepter --}}
        <div class="modal fade" id="modal_accept_reser" data-backdrop="static" data-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Détails de la demande</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body blockui">
                        <form method="post" id="accept_demande" class="form-xhr"
                            action="{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'accepter']) }}">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH" />
                            <div class="row">
                                <div class="col-lg-2 col-xl-3"></div>
                                <div class="col-lg-8 col-xl-6">
                                    <div class="mb-5">
                                        <label class="form-label h6">Prestataire</label>
                                        <select class="form-control basicAutoSelect form-control-lg border-1 form-select" name="prestataire_id" placeholder="Nom ou E-mail..." data-noresults-text="Aucun résultat." data-url="{{route('users.search2json')}}" autocomplete="off"></select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label h6">Prix du service (FCFA)</label>
                                        <input type="text" class="form-control form-control-lg border-0"
                                            name="prix" placeholder="Optionnel" value="{{$reservation->service->prix}}" />
                                    </div>
                                    <div class="text-center py-3">
                                        <button type="submit" class="btn btn-lg btn-primary">Enregistrer</button>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-xl-3"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    @endcan
    @can('reject', App\Models\Reservation::class)
        {{-- Modal Rejeter --}}
        <div class="modal fade" id="modal_reject_reser" data-backdrop="static" data-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="staticBackdropLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel2">Rejeter la demande</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body blockui">
                        <form method="post" id="form_rejeter_demande" class="form-xhr"
                            action="{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'rejeter']) }}">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH" />
                            <div class="row">
                                <div class="col-lg-2 col-xl-3"></div>
                                <div class="col-lg-8 col-xl-6">
                                    <div class="mb-3">
                                        <label class="form-label h6">Motif</label>
                                        <textarea class="form-control form-control-lg border-0 form-control-textarea"
                                            name="justificatif" rows="5"></textarea>
                                    </div>
                                    <div class="text-center py-3">
                                        <button type="submit" class="btn btn-lg btn-primary">Enregistrer</button>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-xl-3"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    @endcan
    @can('update', $reservation)
        {{-- Modal Editer --}}
        <div class="modal fade" id="modal_update_reser" data-backdrop="static" data-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel21" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel21">Modifier la demande</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body blockui">
                        <form method="post" id="form_update_demande" class="form-xhr"
                            action="{{ route('reservations.update', $reservation) }}">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH" />
                            <div class="row">
                                <div class="col-lg-2 col-xl-3"></div>
                                <div class="col-lg-8 col-xl-6">
                                    @can('validate', App\Models\Reservation::class)
                                        <div class="mb-3">
                                            <label class="form-label h6">Service</label>
                                            <select class="form-control" id="service" name="service_id">
                                            @foreach (App\Models\Service::where('actif', true)->get() as $service)
                                                <option value="{{$service->id}}" @if($service->is($reservation->service)) selected @endif>{{$service->label}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label h6">Prix du service</label>
                                            <input type="text" class="form-control form-control-lg border-0"
                                                name="prix" value="{{$reservation->prix}}" />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label h6">Statut</label>
                                            <select class="form-control" id="status" name="status_code">
                                            @foreach (App\Models\Statut::where('statusable', 'Reservation')->get() as $status)
                                                <option value="{{$status->code}}" @if($status->is($reservation->status)) selected @endif>{{$status->label}}</option>
                                            @endforeach
                                            </select>
                                        </div>                                    
                                    @endcan
                                    <div class="mb-3">
                                        <label class="form-label h6">Note</label>
                                        <textarea class="form-control form-control-lg border-0 form-control-textarea"
                                            name="note" rows="5">{{$reservation->note}}</textarea>
                                    </div>
                                    @can('validate', App\Models\Reservation::class)
                                        <div class="mb-3">
                                            <label class="form-label h6">Motif de rejet</label>
                                            <textarea class="form-control form-control-lg border-0 form-control-textarea"
                                                name="justificatif" rows="5">{{$reservation->justificatif}}</textarea>
                                        </div>
                                    @endcan
                                    @foreach ($reservation->service->attributs as $attr)
                                    <div class="mb-6">
                                        <label class="form-label h6">
                                            <span>{{$attr->label}}</span>
                                            @if (!empty($attr->description))
                                                <i class="fas fa-exclamation-circle ml-1 fs-2 text-muted" data-toggle="tooltip" title="{{$attr->description}}"></i>
                                            @endif
                                        </label>
                                        @switch($attr->type_champ)
                                            @case('text')
                                                <input type="text" placeholder="{{$attr->label}}" class="form-control form-control-lg border-0" name="attributs[{{$attr->id}}]" @if(($item = $reservation->attributs->where('id', $attr->id)->first())) value="{{$item->pivot->valeur}}" @endif />
                                            @break
                                        
                                            @case('textarea')
                                                <textarea class="form-control form-control-textarea form-control-lg border-0" name="attributs[{{$attr->id}}]" placeholder="{{$attr->label}}">@if(($item = $reservation->attributs->where('id', $attr->id)->first())){{$item->pivot->valeur}}@endif</textarea>
                                            @break
                                            
                                            @case('file')
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="customFile{{$attr->id}}" name="attributs[{{$attr->id}}]">
                                                    <label class="custom-file-label" for="customFile{{$attr->id}}" data-browse="Parcourir">
                                                        @if(($item = $reservation->attributs->where('id', $attr->id)->first()) && !empty($item->pivot->valeur) && $item->pivot->fichier)
                                                            @php
                                                                $arr = json_decode($item->pivot->valeur, true)
                                                            @endphp
                                                            {{ $arr['nom'] }}
                                                        @else
                                                            Choisir un fichier
                                                        @endif
                                                    </label>
                                                </div>
                                            @break
            
                                            @case('files')
                                                <div id="repeater-{{$attr->id}}" class="form-repeaters mb-6">
                                                    <div data-repeater-list="attributs[{{$attr->id}}]">
                                                        <div class="form-group">
                                                            <div class="mb-3">
                                                                @forelse ($reservation->attributs->where('id', $attr->id) as $key => $file)
                                                                    <div data-repeater-item="attributs_item" class="mb-3">
                                                                        <div class="d-flex">
                                                                            <div class="flex-column flex-grow-1">
                                                                                <div class="custom-file">
                                                                                    <input name="file" type="file" class="custom-file-input" id="customFile{{$attr->id}}_{{$key}}" />
                                                                                    <input type="hidden" class="remove-file" value="{{$file->pivot->valeur}}" />
                                                                                    <label class="custom-file-label" for="customFile{{$attr->id}}_{{$key}}" data-browse="Parcourir">
                                                                                        @if(!empty($file->pivot->valeur) && $file->pivot->fichier)
                                                                                            @php
                                                                                                $arr = json_decode($file->pivot->valeur, true)
                                                                                            @endphp
                                                                                            {{ $arr['nom'] }}
                                                                                        @else
                                                                                            Choisir un fichier
                                                                                        @endif
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex-column">
                                                                                <a href="javascript:;" data-repeater-delete class="ml-3 btn btn-outline btn-outline-danger"><i class="fal fa-trash-alt"></i></a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @empty
                                                                    <div data-repeater-item="attributs_item" class="mb-3">
                                                                        <div class="d-flex">
                                                                            <div class="flex-column flex-grow-1">
                                                                                <div class="custom-file">
                                                                                    <input name="file" type="file" class="custom-file-input" id="customFile{{$attr->id}}_{{($rand = mt_rand(100, 1000))}}" />
                                                                                    <input type="hidden" class="remove-file" value="" />
                                                                                    <label class="custom-file-label" for="customFile{{$attr->id}}_{{$rand}}" data-browse="Parcourir">Choisir un fichier</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex-column">
                                                                                <a href="javascript:;" data-repeater-delete class="ml-3 btn btn-outline btn-outline-danger"><i class="fal fa-trash-alt"></i></a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-4">
                                                        <a href="javascript:;" data-repeater-create class="btn btn-outline btn-outline-primary">
                                                            <i class="fal fa-plus"></i> Ajouter un fichier
                                                        </a>
                                                    </div>
                                                </div>
                                            @break
                                                
                                        @endswitch
                                    </div>
                                    @endforeach
                                    <div class="text-center py-3">
                                        <button type="submit" class="btn btn-lg btn-primary">Enregistrer</button>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-xl-3"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    @endcan

    <x-new-message-popup :user="$reservation->user" />
@endsection
@section('specific-js')
    <script src="/vendors/formrepeater/formrepeater.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@master/dist/latest/bootstrap-autocomplete.min.js"></script>
    <script>
        jQuery(function() {
            $('#comment-files').change(function(e){
                let files_count = e.target.files.length
                $('#btn-comment-files').text('Joindre des fichiers '+(files_count > 1 ? '('+files_count+' fichiers)' : '(1 fichier)'))
            })
            
            @if(isset($edit_reservation))
                @can('update', $reservation)
                    const modal_update_reser = new bootstrap.Modal(document.getElementById('modal_update_reser'));
                    modal_update_reser.show();                    
                @endcan
            @endif

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
                        let file = this.querySelector('.remove-file').value;
                        if(file != "") {
                            $.post("{{ route('reservations.switch', ['reservation' => $reservation, 'action' => 'retirer-fichier']) }}", {
                                _token: $("meta[name=csrf-token]").attr("content"),
                                _method: "PATCH",
                                fichier: file
                            })
                            .done((r) => {
                                console.log("File removed.");
                            })
                            .fail((er) => {
                                console.error("Request failed: ", er);
                            });
                        }
                        $(this).slideUp(deleteElement);
                    }
                });
            })



            function doPost(elt) {
                $('#content').waitMe({
                    ...waitMe_config
                });
                $.post(elt.data('href'), {
                        _token: $("meta[name=csrf-token]").attr("content"),
                        _method: elt.attr('data-method'),
                    })
                    .done((r) => {
                        $('#content').waitMe('hide');
                        Swal.fire({
                            text: r.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "D'accord",
                            customClass: {
                                confirmButton: "btn font-weight-bold btn-primary",
                            },
                        }).then(function() {
                            if (typeof r.redirect != "undefined")
                                document.location.href = r.redirect;
                        });
                    })
                    .fail((er) => {
                        $('#content').waitMe('hide');
                        Swal.fire({
                            text: er.responseJSON.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "D'accord",
                            customClass: {
                                confirmButton: "btn font-weight-bold btn-danger",
                            },
                        });
                    });
            }

            $('.switcher').click(function() {
                    var Elt = $(this)
                    if ($(this).hasClass('confirm')) {
                        Swal.fire({
                            text: $(this).data('confirm'),
                            icon: "question",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Oui, je confirme",
                            cancelButtonText: "Non, annuler",
                            customClass: {
                                confirmButton: "btn font-weight-bold btn-danger",
                                cancelButton: "btn font-weight-bold btn-active-light-primary",
                            },
                        }).then(function(result) {
                            if (result.value) {
                                doPost(Elt);
                            }
                        });
                    } else {
                        doPost(Elt);
                }
            });

            @can('validate', $reservation)
                $('.basicAutoSelect').autoComplete({
                    events: {
                        searchPost: function (resultFromServer) {
                            let arr = [];
                            for (let i = 0; i < resultFromServer.length; i++) {
                                let element = resultFromServer[i];
                                arr.push({ "value": element.id, "text": element.prenoms+' '+element.nom})
                            }
                            return arr;
                        }
                    }
                });
            @endcan
        });
    </script>
@endsection
