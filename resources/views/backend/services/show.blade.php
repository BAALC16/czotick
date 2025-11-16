@extends('layouts.master')
@section('title') @lang('translation.sellers-details') @endsection
@section('css')
<link href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" rel="stylesheet">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Services @endslot
@slot('title'){{ $service->label }} @endslot
@endcomponent
<div class="row">
    <div class="col-xxl-3">
        <div class="card">
            <div class="card-body text-center p-4">
                <img src="{{ $service->image_url }}" alt="" width="200" />
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="fw-medium text-muted mb-0">Vues</p>
                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="{{ $service->views }}">0</span></h2>
                    </div>
                    <div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info rounded-circle fs-2">
                                <i data-feather="eye" class="text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div> <!-- end card-->
    </div>
    <!--end col-->

    <div class="col-xxl-9">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">{{ $service->label }} <span class="badge bg-secondary">@money($service->prix)</span>@if($service->actif) <span class="badge bg-success">Actif</span> @else <span class="badge bg-danger">Inactif</span> @endif</h5>
                    <div class="flex-shrink-0">
                        @can('update', $service)
                            <a href="{{ route('services.edit', ['service' => $service, 'continue' => url()->full()]) }}" class="btn btn-warning add-btn btn-sm"><i class="ri-edit-line align-bottom me-1"></i> Modifier</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="text-muted">
                    {!! $service->description !!}
                </div>
            </div>
        </div>
        <!--end card-->
        <div class="card">
            <div class="card-header">
                <div class="d-flex">
                    <h5 class="card-title flex-grow-1 mb-0">Attributs</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="text-muted">
                    @if($service->attributs->isNotEmpty())
                    <div class="row mt-4">
                        @foreach ($service->attributs as $item)
                        <div class="col-lg-3 col-sm-6">
                            <div class="p-2 border border-dashed rounded">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div
                                            class="avatar-title rounded bg-transparent text-success fs-24">
                                            <i class="ri-file-copy-2-fill"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="text-muted mb-1">{{ App\Models\Attribut::types_champ[$item->type_champ] }}</p>
                                        <h5 class="mb-0">{{ $item->label }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!--end card-->

        <div class="card" id="orderList">
            <div class="card-header">
                <div class="d-flex">
                    <h5 class="card-title flex-grow-1 mb-0">Toutes les Demandes</h5>
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
                                    <th class="sort" data-sort="service">Service</th>
                                    <th class="sort" data-sort="user">Utilisateur</th>
                                    <th class="sort" data-sort="owner">Assign&eacute; &agrave;</th>
                                    <th class="sort" data-sort="status">Statut</th>
                                    <th class="sort" data-sort="date">Date</th>
                                    <th class="sort" data-sort="city">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                            @forelse ($reservations->sortByDesc('created_at') as $row)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td class="product_name">
                                      @can('view', $row->service)
                                        <a class="font-weight-normal" href="{{route('services.show', $row->service)}}">{{$row->service->label}}</a>
                                      @else
                                        {{$row->service->label}}
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
                                <td colspan="7" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
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

                    <div>
                        <h5 class="fs-14 mb-3">Retours Client</h5>
                    </div>
                    <div class="row gy-4 gx-0">
                        <div class="col-lg-4">
                            <div>
                                <div class="pb-3">
                                    <div class="bg-light px-3 py-2 rounded-2 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                @if($avg_rating>=1 && $avg_rating<2)
                                                    <div
                                                        class="fs-16 align-middle text-warning">
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-line"></i>
                                                        <i class="ri-star-line"></i>
                                                        <i class="ri-star-line"></i>
                                                        <i class="ri-star-line"></i>
                                                    </div>
                                                @elseif($avg_rating>=2 && $avg_rating<3)
                                                    <div
                                                        class="fs-16 align-middle text-warning">
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-line"></i>
                                                        <i class="ri-star-line"></i>
                                                        <i class="ri-star-line"></i>
                                                    </div>

                                                @elseif($avg_rating>=3 && $avg_rating<4)
                                                    <div
                                                        class="fs-16 align-middle text-warning">
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-line"></i>
                                                        <i class="ri-star-line"></i>
                                                    </div>

                                                @elseif($avg_rating>=4 && $avg_rating<5)
                                                    <div
                                                        class="fs-16 align-middle text-warning">
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-line"></i>
                                                    </div>
                                                @elseif($avg_rating>=5 && $avg_rating<6)
                                                    <div
                                                        class="fs-16 align-middle text-warning">
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-shrink-0">
                                                <h6 class="mb-0">{{ $avg_rating }} out of 5</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-muted">Total <span
                                                class="fw-medium">{{ number_format($total,0) }}</span> reviews
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="row align-items-center g-2">
                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0">5 star</h6>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="p-2">
                                                <div
                                                    class="progress animated-progress progress-sm">
                                                    <div class="progress-bar bg-success"
                                                         role="progressbar"
                                                         style="width: {{ $prFive }}%"
                                                         aria-valuenow="{{ $prFive }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0 text-muted">{{ $fiveStar }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->

                                    <div class="row align-items-center g-2">
                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0">4 star</h6>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="p-2">
                                                <div
                                                    class="progress animated-progress progress-sm">
                                                    <div class="progress-bar bg-success"
                                                         role="progressbar"
                                                         style="width: {{ $prFour }}%"
                                                         aria-valuenow="{{ $prFour }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0 text-muted">{{ $fourStar }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->

                                    <div class="row align-items-center g-2">
                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0">3 star</h6>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="p-2">
                                                <div
                                                    class="progress animated-progress progress-sm">
                                                    <div class="progress-bar bg-success"
                                                         role="progressbar"
                                                         style="width: {{ $prThree }}%"
                                                         aria-valuenow="{{ $prThree }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0 text-muted">{{ $threeStar }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->

                                    <div class="row align-items-center g-2">
                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0">2 star</h6>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="p-2">
                                                <div
                                                    class="progress animated-progress progress-sm">
                                                    <div class="progress-bar bg-warning"
                                                         role="progressbar"
                                                         style="width: {{ $prTwo }}%"
                                                         aria-valuenow="{{ $prTwo }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0 text-muted">{{ $twoStar }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->

                                    <div class="row align-items-center g-2">
                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0">1 star</h6>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="p-2">
                                                <div
                                                    class="progress animated-progress progress-sm">
                                                    <div class="progress-bar bg-danger"
                                                         role="progressbar"
                                                         style="width: {{ $prOne }}%"
                                                         aria-valuenow="{{ $prOne }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="p-2">
                                                <h6 class="mb-0 text-muted">{{ $oneStar }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row -->
                                </div>
                            </div>
                        </div>
                        <!-- end col -->

                        <div class="col-lg-8">
                            <div class="ps-lg-4">
                                <div class="d-flex flex-wrap align-items-start gap-3">
                                    <h5 class="fs-14">Reviews: </h5>
                                </div>

                                <div class="me-lg-n3 pe-lg-4" data-simplebar
                                     style="max-height: 225px;">
                                    <ul class="list-unstyled mb-0">
                                        @foreach($reviews as $review)
                                            <li class="py-2">
                                                <div
                                                    class="border border-dashed rounded p-3">
                                                    <div
                                                        class="d-flex align-items-start mb-3">
                                                        <div class="hstack gap-3">
                                                            <div
                                                                class="badge rounded-pill bg-success mb-0">
                                                                <i class="mdi mdi-star"></i> {{ number_format($review->rating) }}
                                                            </div>
                                                            <div class="vr"></div>
                                                            <div class="flex-grow-1">
                                                                <p class="text-muted mb-0">
                                                                    {{ $review->review }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-end">
                                                        <div class="flex-grow-1">
                                                            <h5 class="fs-14 mb-0">{{ $review->user->name }}
                                                            </h5>
                                                        </div>

                                                        <div class="flex-shrink-0">
                                                            <p
                                                                class="text-muted fs-13 mb-0">
                                                                {{ $review->created_at }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach



                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end Ratings & Reviews -->
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
<script src="{{ URL::asset('assets/libs/nouislider/nouislider.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/wnumb/wnumb.min.js') }}"></script>
<script src="assets/libs/gridjs/gridjs.min.js"></script>
<script src="https://unpkg.com/gridjs/plugins/selection/dist/selection.umd.js"></script>
<script src="assets/libs/apexcharts/apexcharts.min.js"></script>
<script src="assets/libs/swiper/swiper.min.js"></script>
<script src="{{ URL::asset('assets/js/pages/seller-details.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
