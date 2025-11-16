@extends('layouts.master')
@section('title')
@if ($edit)
    {{ $opportunity->title }}
@else
    Ajouter une opportunité
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
Opportunités
@endslot
@slot('title')
    @if ($edit)
        Editer une opportunité
    @else
        Ajouter un opportunité
    @endif
@endslot
@endcomponent
<form method="POST" id="form" @if ($edit) action="{{ route('opportunities.update', $opportunity) }}" @else
    action="{{ route('opportunities.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
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
                        <label class="input-group-text" for="project-title-input">Libellé </label>
                        <input type="text" class="form-control" id="project-title-input" name="title" @if($edit)
                            value="{{ $opportunity->title }}" @endif />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="">Description</label>
                        <textarea id="description" name="content" rows="8">@if ($edit) {!! $opportunity->content !!}@endif</textarea>
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
                    <input type="file" id="opportunity" name="opportunityImage" accept="image/*" class="filepond" data-show-loader="true"
                        data-allowed-file-extensions="jpg jpeg png gif svg" data-max-file-size="20MB" />
                    @if ($edit)
                    <small>Acienne image</small>
                    <div class="col-4">
                        <a><img src="/public{{ $opportunity->image }}" alt="{{$opportunity->title}}" style="width:100%;">
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
    var opportunity={!! json_encode($opportunityImage) !!};
</script>
<script src="{{ URL::asset('assets/js/pages/opportunity_form_edit.js') }}"></script>
@else
    <script src="{{ URL::asset('assets/js/pages/opportunity_form.js') }}"></script>
@endif
<script src="{{ URL::asset('assets/js/app.min.js') }}"></script>

@endsection
