@extends('layouts.master')
@section('title') D&eacute;tails de la location @endsection
@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/glightbox/glightbox.min.css') }}" type="text/css" />
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Gestion Immobili&egrave;re @endslot
        @slot('title') D&eacute;tails de la location @endslot
    @endcomponent
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#property-details" role="tab">
                                        D&eacute;tails
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#property-documents" role="tab">
                                        Documents
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content text-muted">
                        <div class="tab-pane fade show active" id="property-details" role="tabpanel">
                            <div class="row gx-lg-5">
                                <div class="col-xl-3">
                                    <div class="product-img-slider sticky-side-div">
                                        <div class="swiper product-thumbnail-slider p-2 rounded bg-light">
                                            <div class="swiper-wrapper">
                                            @if (!($rent->property->gallery->isEmpty()))
                                                @foreach($rent->property->gallery as $gallery)
                                                @if (Storage::disk('public')->exists('property/gallery/'.$gallery->name))
                                                <div class="swiper-slide">
                                                    <a class="image-popup" href="{{Storage::url('property/gallery/'.$gallery->name)}}">
                                                    <img src="{{Storage::url('property/gallery/'.$gallery->name)}}" alt="{{$rent->property->title}}" class="img-fluid d-block"/>
                                                </a>
                                                </div>
                                                @endif
                                                @endforeach
                                            @endif
                                            @if (!empty($rent->property->floor_plan) && Storage::disk('public')->exists('property/floor/'.$rent->property->floor_plan))
                                                <div class="swiper-slide">
                                                    <a class="image-popup" href="{{Storage::url('property/floor/'.$rent->property->floor_plan)}}">
                                                    <img src="{{Storage::url('property/floor/'.$rent->property->floor_plan)}}" alt="{{$rent->property->title}}" class="img-fluid d-block" data-gtf-mfp="true"/>
                                                </a>
                                                </div>
                                            @endif
                                            </div>
                                            <div class="swiper-button-next"></div>
                                            <div class="swiper-button-prev"></div>
                                        </div>
                                        <!-- end swiper thumbnail slide -->
                                        <div class="swiper product-nav-slider mt-2">
                                            <div class="swiper-wrapper">
                                            @if (!($rent->property->gallery->isEmpty()))
                                                @foreach($rent->property->gallery as $gallery)
                                                @if (Storage::disk('public')->exists('property/gallery/'.$gallery->name))
                                                <div class="swiper-slide">
                                                    <img src="{{Storage::url('property/gallery/'.$gallery->name)}}" alt="{{$rent->property->title}}"
                                                        class="img-fluid d-block" />
                                                </div>
                                                @endif
                                                @endforeach
                                            @endif
                                            @if (!empty($rent->property->floor_plan) && Storage::disk('public')->exists('property/floor/'.$rent->property->floor_plan))
                                                <div class="swiper-slide">
                                                    <img src="{{Storage::url('property/floor/'.$rent->property->floor_plan)}}" alt="{{$rent->property->title}}"
                                                        class="img-fluid d-block" />
                                                </div>
                                            @endif
                                            </div>
                                        </div>
                                        <!-- end swiper nav slide -->
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-xl-8">
                                    <div class="mt-xl-0 mt-5">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <h4>{{ $rent->property->title }}@if ($rent->property->layoutType) - {{ $rent->property->layoutType->name }} @endif</h4>
                                                <div class="hstack gap-3 flex-wrap icon-demo-content">
                                                    <div><a href="#" class="text-primary d-block">{{ $rent->property->fullAddress() }}</a></div>
                                                    <div class="vr"></div>
                                                    <div class="text-muted"><span class="text-body fw-medium">{{ $rent->property->area }} m&sup2;</span>
                                                    </div>
                                                    @if ($rent->property->floor > -1)
                                                    <div class="vr"></div>
                                                    <div class="text-muted"><i class="las la-layer-group"></i><span class="text-body fw-medium">{{ $rent->property->floor }} / {{ $rent->property->floor_max }}</span>
                                                    </div>
                                                    @endif
                                                    <div class="vr"></div>
                                                    <div class="text-muted"><i class="las la-bed"></i><span class="text-body fw-medium">{{ $rent->property->bedroom }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div class="text-muted"><i class="las la-bath"></i><span class="text-body fw-medium">{{ $rent->property->bathroom }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div class="text-muted">Publi&eacute; le : <span class="text-body fw-medium">{{ $rent->property->dateCreated() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
                                            <div class="text-muted fs-16">
                                                <span class="mdi mdi-star text-warning"></span>
                                                <span class="mdi mdi-star text-warning"></span>
                                                <span class="mdi mdi-star text-warning"></span>
                                                <span class="mdi mdi-star text-warning"></span>
                                                <span class="mdi mdi-star text-warning"></span>
                                            </div>
                                            <div class="text-muted">( {{ number_format($total,0) }} Retour Client )</div>
                                        </div>

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Description :</h5>
                                            <p>{!! $rent->property->description !!}</p>
                                        </div>

                                        @if (!empty($rent->property->nearby))
                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Environnement :</h5>
                                            <p>{!! $rent->property->nearby !!}</p>
                                        </div>
                                        @endif

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Options :</h5>
                                            <div class="btn-group mb-3 mt-md-0" role="group">
                                            @foreach($rent->property->features()->get() as $feature)
                                                <button type="button" class="btn btn-soft-primary waves-effect waves-light" id="features-{{$feature->id}}"><i class="{{$feature->icon}}"></i> {{$feature->name}}</button>
                                            @endforeach
                                            </div>
                                        </div>

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Caution :</h5>
                                            <p>{{ $rent->property->guarantee }} FCFA</p>
                                        </div>

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Loyer :</h5>
                                            <p>{{ $rent->property->price }} FCFA</p>
                                        </div>
                                        <!-- end card body -->
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="property-documents" role="tabpanel">
                            <div class="row gx-lg-5">
                                <div>
                                    <div class="d-flex align-items-center mb-4">
                                        <h5 class="flex-grow-1 fs-16 mb-0" id="filetype-title"></h5>
                                        @can('update', $rent)
                                            <div class="flex-shrink-0">
                                                <a href="{{ route('rent-documents.create.document', $rent) }}" class="btn btn-success add-btn" id="create-btn">Ajouter un document </a>
                                            </div>
                                        @endcan
                                    </div>

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
                                                    <th class="sort" data-sort="name">Nom</th>
                                                    <th class="sort" data-sort="size">Taille</th>
                                                    <th class="sort" data-sort="date">Date</th>
                                                    <th class="sort" data-sort="action">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                @forelse ($rentDocuments as $rentDocument)
                                                <tr>
                                                    <th scope="row">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="checkAll"
                                                                value="option1">
                                                        </div>
                                                    </th>
                                                    <td>
                                                        {{ $rentDocument->name }}
                                                    </td>
                                                    <td>
                                                        {{ $rentDocument->size }}
                                                    </td>
                                                    <td>
                                                        {{ Carbon::parse($rentDocument->created_at)->isoFormat("DD MMM YYYY") }}
                                                    </td>

                                                    <td>
                                                        <ul class="list-inline hstack gap-2 mb-0">
                                                            <li class="list-inline-item edit" data-bs-toggle="tooltip"
                                                                data-bs-trigger="hover" data-bs-placement="top" title="Télécharger">
                                                                <a target="_blank" href="{{ route('rent-documents.download.file', ['rent_document' => $rentDocument, 'continue' => url()->full()]) }}"
                                                                    class="text-primary d-inline-block edit-item-btn">
                                                                    {{-- href="{{ Storage::url($rentDocument->url) }}" --}}
                                                                    <i class="ri-download-2-line fs-16"></i>
                                                                </a>
                                                            </li>
                                                            @can('update', $rent)
                                                                <li class="list-inline-item edit" data-bs-toggle="tooltip"
                                                                    data-bs-trigger="hover" data-bs-placement="top" title="Modifier">
                                                                    <a href="{{ route('rent-documents.edit.document', ['rent_document' => $rentDocument, 'rent' => $rent, 'continue' => url()->full()]) }}"
                                                                        class="text-primary d-inline-block edit-item-btn">
                                                                        <i class="ri-pencil-fill fs-16"></i>
                                                                    </a>
                                                                </li>

                                                                <li class="list-inline-item" data-bs-toggle="tooltip"
                                                                    data-bs-trigger="hover" data-bs-placement="top" title="Supprimer">
                                                                    <a data-href="{{ route('rent-documents.destroy.document', ['rent_document' => $rentDocument, 'rent' => $rent->id]) }}" class="click-to-delete-row"
                                                                        data-confirm="Souhaitez-vous vraiment supprimer ce document ?"
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
                    <!-- end row -->
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
@section('script')
    <!-- glightbox js -->
    <script src="{{ URL::asset('assets/libs/glightbox/glightbox.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/property-show.js') }}"></script>
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
    <script src="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/property_form.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
