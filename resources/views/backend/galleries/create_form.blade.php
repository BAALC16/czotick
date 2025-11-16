@extends('layouts.master')
@section('title')
@if ($edit)
    Editer images {{ $category->label }}
@else
    Ajouter images {{ $category->label }} 
@endif
@endsection
@section('css')
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" >
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@livewireStyles()
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1')
Galérie
@endslot
@slot('title')
    @if ($edit)
        Editer images {{ $category->label }}
    @else
        Ajouter images {{ $category->label }} 
    @endif
@endslot
@endcomponent
<form method="POST" @if($edit) action="{{ route('galleries.update', $category->id) }}" @else action="{{ route('galleries.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
    @csrf
    <input type="hidden" name="_method" @if($edit) value="patch" @else value="post" @endif />

    @if(!empty(request('continue')))
        <input type="hidden" name="continue" value="{{ request('continue') }}" />
    @endif

    <input type="hidden" name="category" value="{{$category->id}}">
    <div class="row  justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Images</h5>
                </div>
                <div class="card-body">
                    <input type="file" id="gallery" name="galleryImage[]" accept="image/*" class="filepond" data-show-loader="true" data-allowed-file-extensions="jpg jpeg png gif svg" data-max-file-size="30MB" data-max-files="1000" />
                </div>
            </div>
            <!-- end card -->
            <div class="text-end mb-4 text-center">
                <button type="submit" class="btn btn-success w-sm">Enregistrer</button>
            </div>
        </div>
       
    </div>
    <!-- end row -->
</form>
@endsection
@livewireScripts()
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
    <!-- filepond js -->
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="{{ URL::asset('assets/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
    @if($edit)
        <script>
            var images={!! json_encode($gallery) !!};
        </script>
        <script src="{{ URL::asset('assets/js/pages/gallery_form_edit.js') }}"></script>
    @else
        <script src="{{ URL::asset('assets/js/pages/gallery_form.js') }}"></script>
    @endif
    <script src="{{ URL::asset('assets/js/pages/form-wizard.init.js') }}"></script>

@endsection
