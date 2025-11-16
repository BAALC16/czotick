@extends('layouts.master')
@section('title')
  @if($edit) {{ $service->label }} @else Configuration du blog @endif
@endsection
@section('css')
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Blog @endslot
@slot('title') Configuration @endslot
@endcomponent
                        <form method="POST" @if($edit) action="{{ route('blog.configuration.update', $service) }}" @else action="{{ route('services.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
                        @csrf
                        <input type="hidden" name="_method" @if($edit) value="patch" @else value="post" @endif />

                        @if(!empty(request('continue')))
                            <input type="hidden" name="continue" value="{{ request('continue') }}" />
                        @endif
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Configurations membres</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="repeater">
                                            <div data-repeater-list="attributs">
                                            @if($edit && $service->attributs->isNotEmpty())
                                                @foreach ($service->attributs as $item)
                                                <div data-repeater-item="attributs_item">
                                                    <div class="row">
                                                        <div class="col-10">
                                                            <div class="mb-3 input-group">
                                                                <label class="input-group-text">Points par d&eacute;faut</label>
                                                                <input class="form-control" name="label"  value="{{ $service->bonusRegister }}" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                            <div data-repeater-item="attributs_item">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3 input-group">
                                                            <label class="input-group-text">Points par d&eacute;faut</label>
                                                            <input class="form-control" name="label" />
                                                         </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end card body -->
                                </div>
                                <!-- end card -->
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Configurations Annonces</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="repeater">
                                            <div data-repeater-list="attributs">
                                            @if($edit && $service->attributs->isNotEmpty())
                                                @foreach ($service->attributs as $item)
                                                <div data-repeater-item="attributs_item">
                                                    <div class="row">
                                                        <div class="col-10">
                                                            <div class="mb-3 input-group">
                                                                <label class="input-group-text">Points par Annonce par jour </label>
                                                                <input class="form-control" name="label"  value="{{ $service->creditPostDaily }}" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                            <div data-repeater-item="attributs_item">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3 input-group">
                                                            <label class="input-group-text">Points par Annonce par jour </label>
                                                            <input class="form-control" name="label" />
                                                         </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end card body -->
                                </div>
                                <!-- end card -->
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Configurations Blog</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="repeater">
                                            <div data-repeater-list="attributs">
                                            @if($edit && $service->attributs->isNotEmpty())
                                                @foreach ($service->attributs as $item)
                                                <div data-repeater-item="attributs_item">
                                                    <div class="row">
                                                        <div class="col-10">
                                                            <div class="mb-3 input-group">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                            <div data-repeater-item="attributs_item">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3 input-group">
                                                         </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end card body -->
                                </div>
                                <!-- end card -->
                            </div>

                        </div>
                        <div class="text-center mb-4">
                            <button type="submit" class="btn btn-success w-sm">Enregistrer</button>
                        </div>
                        <!-- end row -->
                        </form>

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
        <script src="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/pages/property_form.js') }}"></script>
        <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
        @endsection
