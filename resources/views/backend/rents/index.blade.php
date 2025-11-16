@extends('layouts.master')
@section('title') Liste des locations @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Blog @endslot
@slot('title') Toutes les locations @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des locations</h5>
                    <div class="flex-shrink-0">
                        @can('create', App\Models\Rent::class)
                        <a href="{{ route('rents.create') }}" class="btn btn-success add-btn" id="create-btn">Ajouter
                        </a>
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
                                            <input class="form-check-input" type="checkbox" id="checkAll"
                                                value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="product_name">Libell&eacute;</th>
                                    <th class="sort" data-sort="agent">Agent</th>
                                    <th class="sort" data-sort="locataire">Locataire</th>
                                    <th class="sort" data-sort="period">Période</th>
                                    <th class="sort" data-sort="amount">Loyer</th>
                                    <th class="sort" data-sort="status">Statut</th>
                                    <th class="sort" data-sort="action">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                @forelse ($rents as $rent)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll"
                                                value="option1">
                                        </div>
                                    </th>
                                    <td>
                                        {{ $rent->property->title }}
                                    </td>
                                    <td>
                                        {{ $rent->agent->nom }} {{ $rent->agent->prenoms }}
                                    </td>
                                    <td>
                                        {{ $rent->owner->nom }} {{ $rent->owner->prenoms }}
                                    </td>
                                    <td>
                                        {{ Carbon::parse($rent->startDay)->isoFormat("DD MMM YYYY") }} au {{ Carbon::parse($rent->endDay)->isoFormat("DD MMM YYYY") }}
                                    </td>
                                    <td>
                                        {{ $rent->property->price }} FCFA
                                    </td>
                                    <td class="status">
                                        @if($rent->payment == 1)
                                            <span class="badge badge-soft-success text-uppercase">Payé</span>
                                        @else
                                            <span class="badge badge-soft-warning text-uppercasew">En attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                            @can('view', $rent)
                                            <li class="list-inline-item" data-bs-toggle="tooltip"
                                                data-bs-trigger="hover" data-bs-placement="top" title="D&eacute;tails">
                                                <a href="{{ route('rents.show', $rent) }}"
                                                    class="text-primary d-inline-block">
                                                    <i class="ri-eye-fill fs-16"></i>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('update', $rent)
                                                @if($rent->payment == 0)
                                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activer">
                                                        <a href="{{ route('rents.switch', ['rent' => $rent, 'action' => 'accept']) }}" class="text-success d-inline-block edit-item-btn">
                                                            <i class="ri-check-fill fs-16"></i>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Désactiver">
                                                        <a href="{{ route('rents.switch', ['rent' => $rent, 'action' => 'reject']) }}" class="text-danger d-inline-block edit-item-btn">
                                                            <i class="ri-close-fill fs-16"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="list-inline-item" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" data-bs-placement="top" title="Modifier">
                                                    <a href="{{ route('rents.edit', $rent) }}"
                                                        class="text-primary d-inline-block">
                                                        <i class="ri-pencil-fill fs-16"></i>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('delete', $rent)
                                            <li class="list-inline-item" data-bs-toggle="tooltip"
                                                data-bs-trigger="hover" data-bs-placement="top" title="Supprimer">
                                                <a data-href="{{ route('rents.destroy.rent', $rent) }}" class="click-to-delete-row"
                                                    data-confirm="Souhaitez-vous vraiment supprimer cette location ?"
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
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px">
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
                                << </a>
                                    <ul class="pagination listjs-pagination mb-0"></ul>
                                    <a class="page-item pagination-next" href="#">
                                        >>
                                    </a>
                        </div>
                    </div>
                </div>

                <div id="ModalRent" class="modal fade show" tabindex="-1" aria-labelledby="myModalLabel" style="display: hidden;" aria-modal="true" role="dialog">
                    <div class="modal-dialog relative w-auto pointer-events-none">
                        <div class="modal-content border border-danger shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                            <div class="modal-header flex flex-shrink-0 items-center justify-between p-4 border-b border-gray-200 rounded-t-md">
                                <h5 class="text-xl font-medium leading-normal text-gray-800" id="exampleModalLabel">Vente de points</h5>
                                <button type="button" class="btn-close box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body relative p-4">
                                <form method="POST" action="http://127.0.0.1:8000/backend/selling">
                                    <input type="hidden" name="_token" value="iH8KVvJZ9QlptamkY96aWPu99oj0ftvcg4ROeiWn">                    <input type="hidden" name="_method" value="GET">
                                    <div class="mb-3">
                                        <label for="email" class="col-form-label">E-mail:</label>
                                        <input type="email" name="email" class="form-control" id="email">
                                    </div>

                                    <div class="mb-3">
                                        <label for="amount" class="col-form-label">Nombre de point(s)</label>
                                        <input type="number" name="points" class="form-control" id="amount">
                                    </div>
                                    <div class="modal-footer flex flex-shrink-0 flex-wrap items-center justify-end p-4 border-t border-gray-200 rounded-b-md">
                                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-success">Envoyer</button>
                                    </div>
                                </form>

                            </div>
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
<script src="{{ URL::asset('assets/libs/@ckeditor/@ckeditor.min.js') }}"></script><!-- filepond js -->
<script src="{{ URL::asset('assets/libs/filepond/filepond.min.js') }}"></script>

@endsection
