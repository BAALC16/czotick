@extends('layouts.master')
@section('title')
@if ($edit)
{{ $activity->title }}
@else
Ajouter une activité
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
Articles
@endslot
@slot('title')
    @if ($edit)
        Editer une activiter
    @else
        Ajouter une activité
    @endif
@endslot
@endcomponent
<form method="POST" id="form" @if ($edit) action="{{ route('activities.update', $activity) }}" @else
    action="{{ route('activities.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
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
                        <label class="input-group-text" for="project-title-input">Titre de l'activité</label>
                        <input type="text" class="form-control" id="project-title-input" name="title" @if($edit)
                            value="{{ $activity->title }}" @endif />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="">Description</label>
                        <textarea id="description" name="content" rows="8"> @if ($edit) {!! $activity->content !!}@endif </textarea>
                    </div>
                    <label class="" for="">Programme</label>
                    @forelse($programs as $program)
                        <div class="input-group mb-2">
                            <div class="form-check">
                                <input class="form-check-input" value="{{ $program->id }}" type="radio" name="program_id" id="{{ $program->id }}" @if($edit) @if($program->id == $activity->program_id) checked @endif @endif>
                                <label class="form-check-label" for="{{ $program->id }}">
                                {{ $program->title }}
                                </label>
                            </div>
                        </div>
                    @empty
                    @endforelse
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Participation Sénateur</label>
                        <input type="text" class="form-control" id="project-title-input" name="senateur" @if($edit)
                            value="{{ $activity->senateur }}" @endif />
                        <span class="input-group-text" id="basic-addon2">FCFA</span>
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Participation Membre</label>
                        <input type="text" class="form-control" id="project-title-input" name="membre" @if($edit)
                            value="{{ $activity->membre }}" @endif />
                        <span class="input-group-text" id="basic-addon2">FCFA</span>
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Participation Etudiant</label>
                        <input type="text" class="form-control" id="project-title-input" name="etudiant" @if($edit)
                            value="{{ $activity->etudiant }}" @endif />
                        <span class="input-group-text" id="basic-addon2">FCFA</span>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="expiry">Date début</label>
                                <input type="date" class="form-control" id="dateStart" name="dateStart" @if($edit) value="{{ Carbon::parse($activity->dateStart)->format('Y-m-d') }}" @endif >
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="expiry">Date fin</label>
                                <input type="date" class="form-control" id="dateEnd" name="dateEnd" @if($edit) value="{{ Carbon::parse($activity->dateEnd)->format('Y-m-d') }}" @endif >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="location">Lieu</label>
                                <input type="text" class="form-control" id="location" name="location" @if($edit) value="{{ $activity->location }}" @endif >
                            </div>
                        </div>
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
                    <input type="file" id="activity" name="image_file" accept="image/*" class="filepond" data-show-loader="true"
                        data-allowed-file-extensions="jpg jpeg png gif svg" data-max-file-size="20MB" />
                    @if ($edit)
                    <small>Acienne image</small>
                    <div class="col-4">
                        <a><img src="/storage/{{ $activity->image }}" alt="{{ $activity->title }}" style="width:100%;">
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
        var activity={!! json_encode($activityImage) !!};
    </script>
    <script src="{{ URL::asset('assets/js/pages/activity_form_edit.js') }}"></script>
@else
    <script src="{{ URL::asset('assets/js/pages/property_form.js') }}"></script>
@endif
<script src="{{ URL::asset('assets/js/app.min.js') }}"></script>

@endsection
