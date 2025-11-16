@extends('layouts.master')
@section('title') @lang('translation.orders') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Utilisateurs @endslot
@slot('title') Tous les  @if(request()->get('role') == "member") Membres @endif @if(request()->get('role') == "admin") Administrateurs @endif @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-5 flex-grow-1">Liste des @if(request()->get('role') == "membre") Membres @endif @if(request()->get('role') == "admin") Administrateurs @endif</h5>
                </div>
            </div>
            <!-- <div class="card-body border border-dashed border-end-0 border-start-0">
                <form>
                    <div class="row g-3">
                        <div class="col-xxl-5 col-sm-6">
                            <div class="search-box">
                                <input type="text" class="form-control search" placeholder="Recherche...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-xxl-2 col-sm-6">
                            <div>
                                <input type="text" class="form-control" data-provider="flatpickr"
                                    data-date-format="d M, Y" data-range-date="true"
                                    id="demo-datepicker" placeholder="Date d'ajout">
                            </div>
                        </div>
                        <div class="col-xxl-2 col-sm-4">
                            <div>
                                <select class="form-control" data-choices data-choices-search-false
                                    name="choices-single-default" id="idStatus">
                                    <option value="">Statut</option>
                                    <option value="all" selected>All</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Inprogress">Inprogress</option>
                                    <option value="Cancelled">Cancelled</option>
                                    <option value="Pickups">Pickups</option>
                                    <option value="Returns">Returns</option>
                                    <option value="Delivered">Delivered</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xxl-1 col-sm-4">
                            <div>
                                <button type="button" class="btn btn-primary w-100"
                                    onclick="SearchData();"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    Filtrer
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div> -->
            <div class="card-body pt-0">
                <div>
                   <!--  <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active All py-3" data-bs-toggle="tab" id="All"
                                href="#home1" role="tab" aria-selected="true">
                                <i class="ri-store-2-fill me-1 align-bottom"></i> Tous les Utilisateurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-3 Actif" data-bs-toggle="tab" id="Actif"
                                href="#delivered" role="tab" aria-selected="false">
                                <i class="ri-checkbox-circle-line me-1 align-bottom"></i> Actifs
                            </a>
                        </li>
                    </ul>
                    -->
                    <div class="table-responsive table-card mb-1">
                        <table class="table table-nowrap align-middle" id="orderTable">
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th scope="col" style="width: 25px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="lastname">Nom</th>
                                    <th class="sort" data-sort="email">Email</th>
                                    <th class="sort" data-sort="date">Date</th>
                                    <th class="sort" data-sort="actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                            @forelse ($users as $u)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td class="name">
                                    @can('view', $u)
                                      <a href="{{route('users.show', $u)}}">{{ $u->full_name }}</a>
                                    @else
                                      {{ $u->full_name }}
                                    @endcan
                                    </td>
                                    <td class="email">
                                      <span class="text-primary pr-1"><i class="las la-envelope"></i></span> {{ $u->email }}
                                    </td>
                                    <td class="date">
                                      {{Carbon\Carbon::parse($u->created_at)->isoFormat('DD MMM YYYY')}}
                                    </td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                          @can('update', $u)
                                            <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Modifier">
                                                <a href="{{route('users.edit', $u)}}" class="text-primary d-inline-block edit-item-btn">
                                                    <i class="ri-pencil-fill fs-16"></i>
                                                </a>
                                            </li>
                                          @endcan
                                          @can('delete', $u)
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Supprimer">
                                                <a href="#deleteOrder" data-confirm="Souhaitez-vous vraiment supprimer ce service ?" data-href="{{route('users.destroy', $u)}}" data-toggle="tooltip" class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal">
                                                    <i class="ri-delete-bin-5-fill fs-16"></i>
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
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                    trigger="loop" colors="primary:#405189,secondary:#0ab39c"
                                    style="width:75px;height:75px">
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

    </div>
    <!--end col-->
</div>
<!--end row--> 
@endsection
@section('script')
<script src="{{ URL::asset('assets/libs/list.js/list.js.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/list.pagination.js/list.pagination.js.min.js') }}"></script>
<!--ecommerce-customer init js -->
<script src="{{ URL::asset('assets/js/pages/ecommerce-order.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
