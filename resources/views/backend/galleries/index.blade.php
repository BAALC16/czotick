@extends('layouts.master')
@section('title') Galérie @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Galérie @endslot
@slot('title') Galérie @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des Images</h5>
                    <div class="flex-shrink-0">
                        <a href="{{ route('galleries.create') }}" class="btn btn-success add-btn" id="create-btn">Ajouter
                        </a>
                        <a href="{{ route('galleries.edit.category') }}" class="btn btn-warning edit-btn" id="edit-btn">Editer
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
                        <div class="col-lg-12">
                            <div class="">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="text-center">
                                                <ul class="list-inline categories-filter animation-nav" id="filter">
                                                    <li class="list-inline-item"><a class="categories active" data-filter="*">Tous</a></li>
                                                    @foreach($categories as $category)
                                                        <li class="list-inline-item"><a class="categories" data-filter=".{{ $category->slug }}">{{ $category->label }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <div class="row gallery-wrapper">
                                                @forelse($galleries as $gallery)
                                                    <div class="element-item col-xxl-3 col-xl-4 col-sm-6 {{ $gallery->category->slug }}" data-category="{{ $gallery->category->slug }}">
                                                        <div class="gallery-box card">
                                                            <div class="gallery-container">
                                                                <a class="image-popup" href="/public/storage/{{ $gallery->image }}" title="">
                                                                    <img class="gallery-img img-fluid mx-auto" src="/public/storage/{{ $gallery->image }}" alt="" />
                                                                    <!-- <div class="gallery-overlay">
                                                                        <h5 class="overlay-caption">Glasses and laptop from above</h5>
                                                                    </div> -->
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty

                                                @endforelse
                                            </div>
                                            <!-- end row -->

                                           <!--  <div class="text-center my-2">
                                                <a href="javascript:void(0);" class="text-success"><i class="mdi mdi-loading mdi-spin fs-20 align-middle me-2"></i> Load More </a>
                                            </div> -->
                                        </div>
                                    </div>
                                    <!-- end row -->
                                </div>
                                <!-- ene card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
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
    <script src="{{ URL::asset('assets/libs/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/gallery.init.js') }}"></script>
    <!-- <script src="//assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="//assets/libs/simplebar/simplebar.min.js"></script>
    <script src="//assets/libs/node-waves/waves.min.js"></script>
    <script src="//assets/libs/feather-icons/feather.min.js"></script>
    <script src="//assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="//assets/js/plugins.js"></script>
    <script src="//assets/libs/glightbox/js/glightbox.min.js"></script>
    <script src="//assets/js/pages/gallery.init.js"></script>
    <script src="//assets/js/app.js"></script> -->
    <script src="{{ URL::asset('assets/js/app.min.js') }}"></script>

@endsection
