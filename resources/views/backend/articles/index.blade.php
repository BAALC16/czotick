@extends('layouts.master')
@section('title') Liste des articles @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Blog @endslot
@slot('title') Tous les articles @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des Articles</h5>
                    <div class="flex-shrink-0">
                        @can('create', App\Models\Article::class)
                        <a href="{{ route('articles.create') }}" class="btn btn-success add-btn" id="create-btn">Ajouter
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
                                    <th class="sort" data-sort="title">Libell&eacute;</th>
                                    <th class="sort" data-sort="category">Vues</th>
                                    <th class="sort" data-sort="date">Date d'Ajout</th>
                                    <th class="sort" data-sort="status">Statut</th>
                                    <th class="sort" data-sort="action">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                @forelse ($articles as $article)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll"
                                                value="option1">
                                        </div>
                                    </th>
                                    <td class="title">
                                        @can('view', $article)
                                        <a href="{{ route('public.single-post', $article) }}"
                                            class="text-dark hover-primary">{{ $article->title }}</a>
                                        @else
                                        {{ $article->title }}
                                        @endcan
                                    </td>
                                    <td>
                                        {{ $article->views }}
                                    </td>
                                    <td class="date">{{ Carbon::parse($article->created_at)->isoFormat("DD MMM YYYY") }}
                                    </td>
                                    <td class="status">
                                        @if($article->actif == 1)
                                            <span class="badge badge-soft-success text-uppercase">Visible</span>
                                        @elseif($article->actif == 2)
                                            <span class="badge badge-soft-warning text-uppercasew">En attente de validation</span>
                                        @elseif($article->actif == 3)
                                            <span class="badge badge-soft-danger text-uppercasew">Rejeté</span>
                                        @else
                                            <span class="badge badge-soft-success text-uppercase">Non visible</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                            <li class="list-inline-item" data-bs-toggle="tooltip"
                                                data-bs-trigger="hover" data-bs-placement="top" title="Voir">
                                                <a href="{{ route('public.single-post', ['slug' => $article->slug, 'continue' => url()->full()]) }}"
                                                    class="text-primary d-inline-block edit-item-btn">
                                                    <i class="ri-eye-fill fs-16"></i>
                                                </a>
                                            </li>
                                            @can('viewAny', App\Models\User::class)
                                                @switch($article->actif)
                                                    @case(1)
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                            <a href="{{ route('articles.switch', ['article' => $article, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                <i class="ri-close-fill label-icon align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                    @break
                                                    @case(2)
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                            <a href="{{ route('articles.switch', ['article' => $article, 'action' => 'accept']) }}" class="text-success d-inline-block">
                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                            <a href="{{ route('articles.switch', ['article' => $article, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                <i class="ri-close-fill align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                    @break
                                                    @default
                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                            <a href="{{ route('articles.switch', ['article' => $article, 'action' => 'accept']) }}" class="text-primary d-inline-block">
                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                            </a>
                                                        </li>
                                                @endswitch
                                            @endcan

                                            @can('update', $article)
                                            <li class="list-inline-item edit" data-bs-toggle="tooltip"
                                                data-bs-trigger="hover" data-bs-placement="top" title="Modifier">
                                                <a href="{{ route('articles.edit', ['article' => $article, 'continue' => url()->full()]) }}"
                                                    class="text-primary d-inline-block edit-item-btn">
                                                    <i class="ri-pencil-fill fs-16"></i>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('delete', $article)
                                            <li class="list-inline-item" data-bs-toggle="tooltip"
                                                data-bs-trigger="hover" data-bs-placement="top" title="Supprimer">
                                                <a data-href="{{ route('article.delele', $article->id) }}" class="click-to-delete-row"
                                                    data-confirm="Souhaitez-vous vraiment supprimer cet article ?"
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
<script src="{{ URL::asset('assets/libs/@ckeditor/@ckeditor.min.js') }}"></script><!-- filepond js -->
<script src="{{ URL::asset('assets/libs/filepond/filepond.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}">
</script>
<script src="{{ URL::asset('assets/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/list.pagination.js/list.pagination.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.min.js') }}"></script>
@endsection
