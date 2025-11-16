@extends('layouts.master')
@section('title') Galérie @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Galérie categories @endslot
@slot('title') Galérie categories @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des Catégories</h5>
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
                                    <th class="sort" data-sort="title">Libell&eacute;</th>
                                    <th class="sort" data-sort="action">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                @forelse ($categories as $category)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll"
                                                value="option1">
                                        </div>
                                    </th>
                                    <td class="title">
                                        {{ $category->label }}
                                    </td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                            <li class="list-inline-item edit" title="Modifier">
                                                <a data-id="{{ $category->id }}" class="btn text-primary d-inline-block editCategory" style="padding: 0px !important" data-bs-toggle="modal" data-bs-target="#categoryLabel" >
                                                    <i class="ri-pencil-fill fs-16"></i>
                                                </a>
                                            </li>
                                            <div class="modal fade" id="editCategoryModal" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="card-title mb-0 flex-grow-1">Editer une Catégorie</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" id="editCategoryForm" class="form-xhr">
                                                                @csrf
                                                                <input type="hidden" name="_method" value="patch" />
                                                                <input type="hidden" class="form-control" name="category" id="category">
                                                                <div class="row g-3">
                                                                    <div class="input-group flex-nowrap">
                                                                        <span class="input-group-text" id="addon-wrapping">Nom</span>
                                                                        <input type="text" class="form-control" name="label" id="label" aria-describedby="addon-wrapping">
                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <div class="hstack gap-2 justify-content-end">
                                                                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Annuler</button>
                                                                            <button type="submit" class="btn btn-success">Valider</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end row-->
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
    <script src="{{ URL::asset('assets/js/app.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $(".editCategory").click(function() {

                $.ajax({
                    type:'POST',
                    url:"{{ route('galleries.category.edit') }}",
                    data:{id:$(this).data('id')},
                    success:function(data) {
                        $("#label").attr("value", data.label);
                        $("#category").attr("value", data.id);
                        $("#editCategoryForm").attr('action', "{{ url("/backend/galleries/category/update/") }}");
                        $("#editCategoryModal").modal('show');
                    }
                });
            });
        });

    </script>
@endsection
