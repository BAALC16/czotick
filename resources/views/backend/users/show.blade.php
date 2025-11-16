@extends('layouts.master')
@section('title') @lang('translation.profile') @endsection
@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}">
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
@endsection
@section('content')
<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg">
        <img src="{{ URL::asset('assets/images/profile-bg.jpg') }}" alt="" class="profile-wid-img" />
    </div>

</div>
<div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
    <div class="row g-4">
        <div class="col-auto">
            <div class="avatar-lg">
                <img src="{{ $user->photo_url }}" alt="{{ $user->full_name }}" class="img-thumbnail rounded-circle avatar-lg" />
            </div>
        </div>
        <!--end col-->
        <div class="col">
            <div class="p-2">
                <h3 class="text-white mb-1">{{ $user->full_name }}</h3>
                <p class="text-white-75">{{ $user->titre ?? 'Utilisateur ' . config('app.name') }}</p>
                <div class="hstack text-white-50 gap-1">
                    @if (!empty($user->ville ))
                    <div class="me-2">
                        <i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>{{ $user->ville ?? '-' }}
                    </div>
                    @endif
                    @if (!empty($user->mobile ))
                    <div class="me-2">
                        <i class="ri-smartphone-line me-1 text-white-75 fs-16 align-middle"></i>{{ $user->mobile ?? '-' }}
                    </div>
                    @endif
                    <div class="me-2">
                        <i class="ri-mail-send-line me-1 text-white-75 fs-16 align-middle"></i>{{ $user->email }}
                    </div>
                    @if (!empty($user->site_web))
                    <div>
                        <i class="ri-ie-line me-1 text-white-75 fs-16 align-middle"></i>{{ $user->site_web }}
                    </div>
                    @endif

                </div>
            </div>
        </div>
        <!--end col-->

        <div class="col-12 col-lg-auto order-last order-lg-0">
            <div class="flex-shrink-0">
                <a href="{{ route('my.profile') }}" class="btn btn-success"><i class="mdi mdi-account-circle align-bottom"></i> Modifiez votre profil</a>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div>

<div class="row">
    <div class="col-lg-12">
        <div>
            <!-- Tab panes -->
            <div class="tab-content pt-4 text-muted">
                <div class="tab-pane active" id="overview-tab" role="tabpanel">
                    <div class="row">
                        <div class="col-xxl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-0">R&ocirc;les ({{ $user->roles->count() }})</h5>
                                        </div>
                                    </div>
                                    <div>
                                        @forelse($user->roles as $r)
                                        <div class="d-flex align-items-center py-3">
                                            <div class="flex-grow-1">
                                                <div>
                                                    <h5 class="fs-14 mb-1">{{ $r->nom }}</h5>
                                                    <p class="fs-13 text-muted mb-0">
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
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <button type="button" class="btn btn-sm btn-outline-success">
                                                    <i class="ri-eye-line align-middle"></i>
                                                </button>

                                            </div>
                                        </div>
                                        @empty
                                        <div class="d-flex align-items-center py-3">
                                            <div class="flex-grow-1">
                                                <div>
                                                    <h5 class="fs-14 mb-1">Aucun r&ocirc;le</h5>
                                                </div>
                                            </div>
                                        </div>
                                        @endforelse                                    
                                        @can('assign', App\Models\Role::class)
                                        <div class="py-4">
                                            <button href="#modal-add-roles" data-toggle="tooltip" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal">Modifier les rôles</button>

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
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close">
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
                                                                        <div class="form-check form-switch form-switch-secondary">
                                                                            <input @if($user->roles->contains($r->id)) checked @endif name="roles[]" value="{{$r->id}}" type="checkbox" class="form-check-input" id="customSwitch{{$r->id}}">
                                                                            <label class="form-check-label" for="customSwitch{{$r->id}}">{{$r->nom}}</label>
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
                                    </div>
                                </div><!-- end card body -->
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-xxl-9">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Pr&eacute;sentation</h5>
                                    @if ($user->introduction)
                                        {{ $user->introduction }}
                                    @else
                                        <em>{{ $user->prenoms }} n'a pas encore rempli son texte de présentation.</em>
                                    @endif
                                </div>
                                <!--end card-body-->
                            </div><!-- end card -->

                            <div class="card" id="orderList">
                                <div class="card-header">
                                    <div class="d-flex">
                                        <h5 class="card-title flex-grow-1 mb-0">Demandes de Service </h5>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div>
                                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                                        </ul>

                                        <div class="table-responsive table-card mb-1">
                                            <table class="table table-nowrap align-middle" id="orderTable">
                                                <thead class="text-muted table-light">
                                                    <tr class="text-uppercase">
                                                        <th class="sort" data-sort="service">Service</th>
                                                        <th class="sort" data-sort="owner">Assign&eacute; &agrave;</th>
                                                        <th class="sort" data-sort="status">Statut</th>
                                                        <th class="sort" data-sort="date">Date</th>
                                                        <th class="sort" data-sort="city">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                                                @forelse ($reservations->sortByDesc('created_at') as $row)
                                                    <tr>
                                                        <td class="product_name">
                                                          @can('view', $row->service)
                                                            <a class="font-weight-normal" href="{{route('services.show', $row->service)}}">{{$row->service->label}}</a>
                                                          @else
                                                            {{$row->service->label}}
                                                          @endcan
                                                        </td>
                                                        <td class="status">
                                                          @if($row->prestataire)
                                                          @can('view', $row->user)
                                                              <a class="font-weight-normal" href="{{route('users.show', $row->prestataire)}}">{{$row->prestataire->full_name}}</a>
                                                          @else
                                                            {{$row->prestataire->full_name}}
                                                          @endcan
                                                          @else
                                                            -
                                                          @endif
                                                        </td>
                                                        <td class="status">
                                                            <span class="badge badge-soft-{{$row->status->color}}">{{$row->status->label}}</span>
                                                        </td>
                                                        <td class="date">
                                                            {{ Carbon::parse($row->created_at)->isoFormat("DD MMM YYYY") }}
                                                        </td>
                                                        <td>
                                                            <ul class="list-inline hstack gap-2 mb-0">
                                                            @can('view', $row)
                                                              <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="D&eacute;tails">
                                                                  <a href="{{route('reservations.show', $row)}}" class="text-primary d-inline-block">
                                                                      <i class="ri-eye-fill fs-16"></i>
                                                                  </a>
                                                              </li>
                                                            @endcan
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @empty
                                                  <tr>
                                                    <td colspan="6" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
                                                  </tr>
                                                @endforelse
                                                    
                                                </tbody>
                                            </table>
                                            <div class="noresult" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px">
                                                    </lord-icon>
                                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                                    <p class="text-muted">We've searched more than 150+ Orders We did
                                                        not find any
                                                        orders for you search.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="pagination-wrap hstack gap-2">
                                                <a class="page-item pagination-prev disabled" href="#">
                                                    <<
                                                </a>
                                                <ul class="pagination listjs-pagination mb-0"></ul>
                                                <a class="page-item pagination-next" href="#">
                                                    >>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card" id="orderList">
                                <div class="card-header  border-0">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0 flex-grow-1">Location / Vente</h5>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div>
                                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                                        </ul>

                                        <div class="table-responsive table-card mb-1">
                                            <table class="table table-nowrap align-middle" id="orderTable">
                                                <thead class="text-muted table-light">
                                                    <tr class="text-uppercase">
                                                        <th class="sort" data-sort="service">Bien</th>
                                                        <th class="sort" data-sort="user">Client</th>
                                                        <th class="sort" data-sort="owner">Agent</th>
                                                        <th class="sort" data-sort="status">Statut</th>
                                                        <th class="sort" data-sort="date">Date</th>
                                                        <th class="sort" data-sort="city">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                                                @forelse ($inquiries->sortByDesc('created_at') as $row)
                                                    <tr>
                                                        <td class="product_name">
                                                          @can('view', $row->property)
                                                            <a class="font-weight-normal" href="{{route('inquiries.show', $row)}}">{{$row->property->title}}</a>
                                                          @else
                                                            <a class="font-weight-normal" target="_blank" href="{{ route('public.property', ['property' => $row->property, 'address' => str_slug($row->property->fullAddress()), 'title' => $row->property->slug]) }}">{{$row->property->title}}</a>
                                                          @endcan
                                                        </td>
                                                        <td class="amount">
                                                        @can('view', $row->user)
                                                            <a class="font-weight-normal" href="{{route('users.show', $row->user)}}">{{$row->user->full_name}}</a>
                                                        @else
                                                            {{$row->user->full_name}}
                                                        @endcan
                                                        </td>
                                                        <td class="status">
                                                          @if($row->agent)
                                                          @can('view', $row->user)
                                                              <a class="font-weight-normal" href="{{route('users.show', $row->agent)}}">{{$row->agent->full_name}}</a>
                                                          @else
                                                            {{$row->agent->full_name}}
                                                          @endcan
                                                          @else
                                                            -
                                                          @endif
                                                        </td>
                                                        <td class="status">
                                                            <span class="badge badge-soft-{{$row->status->color}}">{{$row->status->label}}</span>
                                                        </td>
                                                        <td class="date">
                                                            {{ Carbon::parse($row->created_at)->isoFormat("DD MMM YYYY") }}
                                                        </td>
                                                        <td>
                                                            <ul class="list-inline hstack gap-2 mb-0">
                                                            @can('view', $row)
                                                              <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="D&eacute;tails">
                                                                  <a href="{{route('inquiries.show', $row)}}" class="text-primary d-inline-block">
                                                                      <i class="ri-eye-fill fs-16"></i>
                                                                  </a>
                                                              </li>
                                                            @endcan
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @empty
                                                  <tr>
                                                    <td colspan="6" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
                                                  </tr>
                                                @endforelse
                                                    
                                                </tbody>
                                            </table>
                                            <div class="noresult" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px">
                                                    </lord-icon>
                                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                                    <p class="text-muted">We've searched more than 150+ Orders We did
                                                        not find any
                                                        orders for you search.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="pagination-wrap hstack gap-2">
                                                <a class="page-item pagination-prev disabled" href="#">
                                                    <<
                                                </a>
                                                <ul class="pagination listjs-pagination mb-0"></ul>
                                                <a class="page-item pagination-next" href="#">
                                                    >>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body p-5 text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                                        style="width:90px;height:90px"></lord-icon>
                                                    <div class="mt-4 text-center">
                                                        <h4>Vous &ecirc;tes sur le point de supprimer un service?</h4>
                                                        <p class="text-muted fs-15 mb-4">Toutes les infos seront supprim&eacute;es de la Base de Donn&eacute;es.</p>
                                                        <div class="hstack gap-2 justify-content-center remove">
                                                            <button
                                                                class="btn btn-link link-success fw-medium text-decoration-none"
                                                                data-bs-dismiss="modal"><i
                                                                    class="ri-close-line me-1 align-middle"></i>
                                                                Fermer</button>
                                                            <button class="btn btn-danger" id="delete-record">Oui,
                                                                Supprimer</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end modal -->
                                </div>
                            </div>

                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
            </div>
            <!--end tab-content-->
        </div>
    </div>
    <!--end col-->
</div>
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
        <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
