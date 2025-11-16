@extends('layouts.master')
@section('title') Liste des Promotions @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Points Crédit @endslot
@slot('title') Toutes les Promotions @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des Promotions</h5>
                    <div class="flex-shrink-0">
                          @can('create', App\Models\CreditPointsPromotions::class)
                            <a href="{{ route('credit-points-promotions.create') }}" class="btn btn-success add-btn" id="create-btn" >Ajouter </a>
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
                                <tr class="text-uppercase">
                                    <th scope="col" style="width: 25px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="label">Libell&eacute;</th>
                                    <th class="sort" data-sort="credit_points">Points crédit</th>
                                    <th class="sort" data-sort="amount">Montant</th>
                                    <th class="sort" data-sort="user">Utilisateur</th>
                                    <th class="sort" data-sort="date">Date d'exp</th>
                                    <th class="sort" data-sort="status">Statut</th>
                                    <th class="sort" data-sort="action">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                            @forelse ($promotions as $promotion)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td class="product_name">
                                      @can('view', $promotion)
                                        <a href="{{ route('promotions.show', $promotion) }}" class="text-dark hover-primary">{{ $promotion->title }}</a>
                                      @else
                                        {{ $promotion->title }}
                                      @endcan
                                    </td>
                                    <td>{{$promotion->creditPoints->point}}</td>
                                    <td>{{$promotion->creditPoints->amount}}</td>
                                    <td>{{$promotion->owner->nom}}  {{$promotion->owner->prenoms}}</td>
                                    <td class="date">{{ Carbon::parse($promotion->expiry)->isoFormat("DD MMM YYYY") }}</td>
                                    <td class="status">
                                      @if($promotion->published == 1)
                                        <span class="badge badge-soft-success text-uppercase">Actif</span>
                                      @else
                                        <span class="badge badge-soft-warning text-uppercasew">Inactif</span>
                                      @endif
                                    </td>
                                  
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                          @can('update', $promotion)
                                                @if($promotion->published == 0)  
                                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activer">
                                                        <a href="{{ route('credit-points-promotions.switch', ['promotion' => $promotion, 'action' => 'accept']) }}"" class="text-success d-inline-block edit-item-btn">
                                                            <i class="ri-check-fill fs-16"></i>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Désactiver">
                                                        <a href="{{ route('credit-points-promotions.switch', ['promotion' => $promotion, 'action' => 'reject']) }}"" class="text-danger d-inline-block edit-item-btn">
                                                            <i class="ri-close-fill fs-16"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Modifier">
                                                    <a href="{{ route('credit-points-promotions.edit', ['credit_points_promotion' => $promotion, 'continue' => url()->full()]) }}" class="text-primary d-inline-block edit-item-btn">
                                                        <i class="ri-pencil-fill fs-16"></i>
                                                    </a>
                                                </li>
                                          @endcan
                                          @can('delete', $promotion)
                                            <li class="list-inline-item" data-bs-toggle="tooltip"
                                                data-bs-trigger="hover" data-bs-placement="top" title="Supprimer">
                                                <a data-href="{{ route('credit-points-promotions.destroy', $promotion) }}" class="click-to-delete-row"
                                                    data-confirm="Souhaitez-vous vraiment supprimer cette promotion?"
                                                    data-toggle="tooltip"
                                                    class="text-danger d-inline-block remove-item-btn"
                                                    data-bs-toggle="modal">
                                                    <i class="ri-delete-bin-5-fill fs-16 "></i>
                                                </a>
                                            </li>
                                          @endcan
                                        </ul>
                                    </td>
                                </tr>
                            @empty
                              <tr>
                                <td colspan="5" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
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

                <!-- Modal -->
                <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body p-5 text-center">
                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                    trigger="loop" colors="primary:#405189,secondary:#f06548"
                                    style="width:90px;height:90px"></lord-icon>
                                <div class="mt-4 text-center">
                                    <h4>Vous &ecirc;tes sur le point de supprimer une promotion?</h4>
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
<!--ecommerce-customer init js -->
<script src="{{ URL::asset('assets/js/pages/ecommerce-order.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
