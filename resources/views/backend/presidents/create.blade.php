@extends('layouts.master')
@section('title')
@if ($edit)
{{ $president->lastname }} {{ $president->firstname }}
@else
Ajouter un président
@endif
@endsection
@section('css')
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" >
<link rel="stylesheet"
    href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@livewireStyles()
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1')
    PEL
@endslot
@slot('title')
    @if ($edit)
        Editer un président
    @else
        Ajouter un président
    @endif
@endslot
@endcomponent
<form method="POST" id="form" @if ($edit) action="{{ route('presidents.update', $president) }}" @else
    action="{{ route('presidents.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
    @csrf
    <input type="hidden" name="_method" @if ($edit) value="patch" @else value="post" @endif />
    @if (!empty(request('continue')))
    <input type="hidden" name="continue" value="{{ request('continue') }}" />
    @endif
    <div class="row  justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Nom</label>
                        <input type="text" class="form-control" id="project-title-input" name="firstname" @if($edit)
                            value="{{ $president->firstname }}" @endif />
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Prénoms</label>
                        <input type="text" class="form-control" id="project-title-input" name="lastname" @if($edit)
                            value="{{ $president->lastname }}" @endif />
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Email</label>
                        <input type="email" class="form-control" id="project-title-input" name="email" @if($edit)
                            value="{{ $president->email }}" @endif />
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Fonction</label>
                        <input type="text" class="form-control" id="project-title-input" name="job" @if($edit)
                            value="{{ $president->job }}" @endif />
                    </div>
                    <div class="input-group mb-3">
                        <select class="form-select mb-3" aria-label="Default select example" name="zone">
                            <option value="1" @if($edit) @if($president->zone == 1) selected @endif @endif>Zone 1</option>
                            <option value="2" @if($edit) @if($president->zone == 2) selected @endif @endif>Zone 2</option>
                            <option value="3" @if($edit) @if($president->zone == 3) selected @endif @endif>Zone 3</option>
                            <option value="4" @if($edit) @if($president->zone == 4) selected @endif @endif>Zone 4</option>
                            <option value="5" @if($edit) @if($president->zone == 5) selected @endif @endif>Zone 5</option>
                            <option value="6" @if($edit) @if($president->zone == 6) selected @endif @endif>Zone 6</option>
                            <option value="7" @if($edit) @if($president->zone == 7) selected @endif @endif>Zone 7</option>
                            <option value="8" @if($edit) @if($president->zone == 8) selected @endif @endif>Zone 8</option>
                            <option value="9" @if($edit) @if($president->zone == 9) selected @endif @endif>Zone 9</option>
                        </select>
                    </div>

                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Image mise en avant</h5>
                </div>
                <div class="card-body">
                    <input type="file" id="president" name="image_file" accept="image/*" class="filepond" data-show-loader="true"
                        data-allowed-file-extensions="jpg jpeg png gif svg" data-max-file-size="20MB" data-max-files="1" />
                    @if ($edit)
                    <small>Acienne image</small>

                    <div class="col-4">
                        <a><img src="/public/{{ Storage::url("{$president->image}") }}" alt="{{$president->title}}" style="width:100%;">
                        </a>
                    </div>
                     @endif

                </div>
            </div>
            <!-- end card -->
            <div class="text-end mb-4 text-center">
                <button type="submit" class="btn btn-success w-sm">Enregistrer</button>
            </div>
        </div>
        <!-- end col -->
        {{--
        <livewire:facturation /> --}}
        {{-- INSERER LA COMPASANTE LIVEWIRE --}}
        <!-- end col -->
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
<script src="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}">
</script>
<script
    src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}">
    </script>
<script
    src="{{ URL::asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}">
    </script>
<script src="{{ URL::asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
<script
    src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}">
</script>
@if($edit)
    <script>
        var president={!! json_encode($presidentImage) !!};
    </script>
    <script src="{{ URL::asset('assets/js/pages/president_form_edit.js') }}"></script>
@else
    <script src="{{ URL::asset('assets/js/pages/president_form.js') }}"></script>
@endif
<script src="{{ URL::asset('assets/js/app.min.js') }}"></script>

@endsection
