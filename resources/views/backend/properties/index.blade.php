@extends('layouts.master')
@section('title') Liste des Biens @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Gestion des Biens @endslot
@slot('title') Tous les Biens @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des Biens</h5>
                    <div class="flex-shrink-0">
                          @can('create', App\Models\Property::class)
                            <a href="{{ route('properties.create') }}" class="btn btn-success add-btn" id="create-btn" >Ajouter </a>
                          @endcan
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div>
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                    </ul>

                    <div class="table-responsive table-card mb-1">
                        <table class="table table-nowrap align-middle" id="orderTable">
                            <thead class="text-muted table-light">
                                    <th class="sort" data-sort="name">Titre</th>
                                    <th class="sort" data-sort="agent">Agent</th>
                                    <th class="sort" data-sort="type">Type</th>
                                    <th class="sort" data-sort="purpose">Pour</th>
                                    <th class="sort" data-sort="area"><i class="las la-ruler-combined"></i></th>
                                    <th class="sort" data-sort="views">Vues</th>
                                    <th class="sort" data-sort="action">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                            @forelse ($properties as $property)
                                <tr>
                                    <td class="name"><div class="d-flex align-items-center"> 
                                    @if (!($property->gallery->isEmpty()))
                                        @if (Storage::disk('public')->exists('property/gallery/'.$property->gallery->first()->name))
                                        <div class="flex-shrink-0">
                                            <a href="{{ route('properties.show', $property) }}" class="text-dark hover-primary">
                                            <img src="{{Storage::url('property/gallery/'.$property->gallery->first()->name)}}" alt="{{$property->title}}" class="rounded avatar-sm bg-light">
                                        </a>
                                        </div>
                                        @endif
                                    @endif
                                    <div class="flex-grow-1 ms-2 name">
                                        <h5 class="fs-14 mb-1">
                                      @can('view', $property)
                                        <a href="{{ route('properties.show', $property) }}" class="text-dark hover-primary">{{ $property->title }}</a>
                                      @else
                                        {{ $property->title }}
                                      @endcan
                                    </h5>
                                    <p class="text-muted mb-0">
                                        {{ $property->fullAddress() }}
                                    </p>
                                  </div></div>
                                    </td>
                                    <td>{{$property->user->full_name}}</td>
                                    <td>
                                        <div>
                                            <i class="las {{$property->propertyType->icon}}"></i> {{$property->propertyType->name}}
                                        </div>
                                        <p class="text-muted mb-0">
                                            @if ($property->layoutType)
                                            {{ $property->layoutType->name }}
                                            @endif
                                            @if ($property->published)
                                            <span class="badge badge-soft-success text-uppercase">En ligne</span>
                                            @else
                                            <span class="badge badge-soft-danger text-uppercase">Inactive</span>
                                            @endif
                                        </p>
                                    </td>
                                    <td>{{$property->purpose}}</td>
                                    <td>{{$property->area}} m&sup2;
                                    @if (!empty($property->bedroom))
                                    <i class="las la-bed"></i> {{$property->bedroom}}
                                    @endif
                                    @if (!empty($property->bathroom))
                                    <i class="las la-bath"></i> {{$property->bathroom}}
                                    @endif
                                    </td>
                                    <td>{{$property->views}}</td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                        @can('view', $property)
                                          <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="D&eacute;tails">
                                              <a href="{{ route('properties.show', $property) }}" class="text-primary d-inline-block">
                                                  <i class="ri-eye-fill fs-16"></i>
                                              </a>
                                          </li>
                                        @endcan
                                          @can('update', $property)
                                            <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Modifier">
                                                <a href="{{ route('properties.edit', ['property' => $property, 'continue' => url()->full()]) }}" class="text-primary d-inline-block edit-item-btn">
                                                    <i class="ri-pencil-fill fs-16"></i>
                                                </a>
                                            </li>
                                          @endcan
                                          @can('delete', $property)
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Supprimer">
                                                <a href="#deleteOrder" data-confirm="Souhaitez-vous vraiment supprimer ce sp&eacute;cification ?" data-href="{{ route('properties.destroy', $property) }}" data-toggle="tooltip" class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal">
                                                    <i class="ri-delete-bin-5-fill fs-16"></i>
                                                </a>
                                            </li>
                                          @endcan
                                        </ul>
                                    </td>
                                </tr>
                            @empty
                              <tr>
                                <td colspan="7" class="align-middle">
                                  @can('create', App\Models\Property::class)
                                    <a href="{{ route('properties.create') }}" >Ajouter votre premier bien!</a>
                                  @endcan
                              </td>
                              </tr>
                            @endforelse
                                
                            </tbody>
                        </table>
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
                                    <h4>Vous &ecirc;tes sur le point de supprimer une sp&eacute;cificiation?</h4>
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
@endsection
@section('script')
<script src="{{ URL::asset('assets/libs/list.js/list.js.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/list.pagination.js/list.pagination.js.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
