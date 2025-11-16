@extends('layouts.master')
@section('title')
  @if($edit) {{ $rentDocument->name }} @else Ajouter un Document @endif
@endsection
@section('css')
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Locations @endslot
@slot('title')Ajouter Document @endslot
@endcomponent
                        <form method="POST" @if($edit) action="{{ route('rent-documents.update', $rentDocument) }}" @else action="{{ route('rent-documents.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
                        @csrf
                        <input type="hidden" name="_method" @if($edit) value="patch" @else value="post" @endif />
                        <input type="hidden" name="property_id" value="{{ $rent->property_id }}">
                        <input type="hidden" name="user_id" value="{{ $rent->user_id }}">
                        <input type="hidden" name="rent_id" value="{{ $rent->id }}">
                        @if(!empty(request('continue')))
                            <input type="hidden" name="continue" value="{{ request('continue') }}" />
                        @endif
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="project-title-input">Nom</label>
                                            <input type="text" class="form-control" id="project-title-input" name="name" @if($edit) value="{{ $rentDocument->name }}" @endif  required>
                                        </div>

                                    </div>
                                    <!-- end card body -->
                                </div>
                                <!-- end card -->

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Document</h5>
                                    </div>
                                    <div class="card-body">
                                        <input type="file" name="document_file" class="filepond" data-show-loader="false" data-allowed-file-extensions="jpg jpeg png" data-max-file-size="20MB" />
                                    </div>
                                <!-- end card -->
                                <div class="text-end mb-4">
                                    <button type="submit" class="btn btn-success w-sm">Enregistrer</button>
                                </div>
                            </div>

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
