@extends('layouts.master')
@section('title') @lang('translation.orders') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Biens @endslot
@slot('title') Toutes les Requ&ecirc;tes @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Toutes les Requ&ecirc;tes</h5>
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
                                    <th class="sort" data-sort="type">Type</th>
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
                                        <a class="font-weight-normal" href="{{route('properties.show', $row->property)}}">{{$row->property->title}}</a>
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
                                        @can('view', $row->agent)
                                            <a class="font-weight-normal" href="{{route('users.show', $row->agent)}}">{{$row->agent->full_name}}</a>
                                        @else
                                          {{$row->agent->full_name}}
                                        @endcan
                                        @else
                                          -
                                        @endif
                                    </td>
                                    <td class="type">
                                        {{$row->note}}
                                    </td>
                                    <td class="status">
                                        <span class="badge badge-soft-{{$row->status->color}}">{{$row->status->label}}</span>
                                    </td>
                                    <td class="date">
                                        {{ Carbon::parse($row->created_at)->isoFormat("DD MMM YYYY") }}
                                    </td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                            @can('view', $row->property)
                                                <li>
                                                    <a href="{{route('properties.show', $row->property)}}" class="text-primary d-inline-block" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Voir">
                                                        <i class="ri-eye-fill fs-16"></i>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('view', $row)
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Chat">
                                                    <a href="{{route('inquiries.show', $row)}}" class="text-info d-inline-block">
                                                        <i class="ri-message-3-line fs-16"></i>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('update', $row)
                                                @switch($row->status_code)
                                                    @case("DEM_REJETEE")
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'accept']) }}" class="text-primary d-inline-block">
                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                    @break
                                                    @case("DEM_ACCEPTEE")
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                <i class="ri-close-fill label-icon align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                    @break
                                                    @default
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'accept']) }}" class="text-success d-inline-block">
                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                <i class="ri-close-fill align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                @endswitch
                                                @switch($row->marker)
                                                    @case(1)
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Archiver">
                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'archive']) }}" class="text-warning d-inline-block">
                                                                <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                    @break
                                                    @case(2)
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Selectionner">
                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'shortList']) }}" class="text-primary d-inline-block">
                                                                <i class="ri-star-line align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                    @break
                                                    @default
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Selectionner">
                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'shortList']) }}" class="text-primary d-inline-block">
                                                                <i class="ri-star-line align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Archiver">
                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'archive']) }}" class="text-warning d-inline-block">
                                                                <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                @endswitch
                                            @endcan
                                        </ul>
                                    </td>
                                </tr>
                            @empty
                              <tr>
                                <td colspan="6" class="align-middle">Aucune demande pour le moment.</td>
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
@endsection
@section('script')
<script src="{{ URL::asset('assets/libs/list.js/list.js.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/list.pagination.js/list.pagination.js.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
