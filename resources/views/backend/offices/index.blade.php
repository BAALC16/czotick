@extends('layouts.master')
@section('title') Liste du CDN @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') CDN @endslot
@slot('title') Tous les membres @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste du CDN</h5>
                    <div class="flex-shrink-0">
                        <a href="{{ route('offices.create') }}" class="btn btn-success add-btn" id="create-btn">Ajouter
                        </a>
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
                                    <th class="sort" data-sort="title">Nom</th>
                                    <th class="sort" data-sort="title">Prénoms</th>
                                    <th class="sort" data-sort="title">Fonction</th>
                                    <th class="sort" data-sort="action">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                @forelse ($offices as $office)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll"
                                                value="option1">
                                        </div>
                                    </th>
                                    <td class="title">
                                        {{ $office->firstname }}
                                    </td>
                                    <td class="title">
                                        {{ $office->lastname }}
                                    </td>
                                    <td>
                                        {{ $office->job }}
                                    </td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                            <li class="list-inline-item" data-bs-toggle="tooltip"
                                                data-bs-trigger="hover" data-bs-placement="top" title="D&eacute;tails">
                                                <a href="{{ route('offices.show', $office) }}"
                                                    class="text-primary d-inline-block">
                                                    <i class="ri-eye-fill fs-16"></i>
                                                </a>
                                            </li>

                                           <li class="list-inline-item edit" data-bs-toggle="tooltip"
                                                data-bs-trigger="hover" data-bs-placement="top" title="Modifier">
                                                <a href="{{ route('offices.edit', ['office' => $office, 'continue' => url()->full()]) }}"
                                                    class="text-primary d-inline-block edit-item-btn">
                                                    <i class="ri-pencil-fill fs-16"></i>
                                                </a>
                                            </li>

                                            <li class="list-inline-item" data-bs-toggle="tooltip"
                                                data-bs-trigger="hover" data-bs-placement="top" title="Supprimer">
                                                <a data-href="{{ route('offices.destroy.office', $office) }}" class="click-to-delete-row"
                                                    data-confirm="Souhaitez-vous vraiment supprimer ce membre ?"
                                                    data-toggle="tooltip"
                                                    class="text-danger d-inline-block remove-item-btn"
                                                    data-bs-toggle="modal">
                                                    <i class="ri-delete-bin-5-fill fs-16 "></i>
                                                </a>
                                            </li>
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
