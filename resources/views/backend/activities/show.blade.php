@extends('layouts.master')
@section('title')

Voir une activité

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
    Voir une activité
@endslot
@endcomponent
<form method="POST" id="form" @if ($edit) action="{{ route('activities.update', $activity) }}" @else
    action="{{ route('activities.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
    @csrf
    <input readonly type="hidden" name="_method" @if ($edit) value="patch" @else value="post" @endif />
    @if (!empty(request('continue')))
    <input readonly type="hidden" name="continue" value="{{ request('continue') }}" />
    @endif
    <div class="row  justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Titre de l'activité</label>
                        <input readonly type="text" class="form-control" id="project-title-input" name="title"
                            value="{{ $activity->title }}"  />
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="">Description</label>
                        <input type="text" hidden name="content">
                        <div class="form-field col-lg-12" id="message" style="height: 300px; ">
                             {!! $activity->content !!}
                        </div>
                    </div>
                    <label class="" for="">Programme</label>
                    @forelse($programs as $program)
                        <div class="input-group mb-2">
                            <div class="form-check">
                                <input readonly class="form-check-input" value="{{ $program->id }}" type="radio" name="program_id" id="{{ $program->id }}" @if($program->id == $activity->program_id) checked @endif >
                                <label class="form-check-label" for="{{ $program->id }}">
                                {{ $program->title }}
                                </label>
                            </div>
                        </div>
                    @empty
                    @endforelse
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Participation Sénateur</label>
                        <input type="text" class="form-control" id="project-title-input" name="senateur"
                            value="{{ $activity->senateur }}"/>
                        <span class="input-group-text" id="basic-addon2">FCFA</span>
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Participation Membre</label>
                        <input type="text" class="form-control" id="project-title-input" name="membre"
                            value="{{ $activity->membre }}" />
                        <span class="input-group-text" id="basic-addon2">FCFA</span>
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="project-title-input">Participation Etudiant</label>
                        <input type="text" class="form-control" id="project-title-input" name="etudiant"
                            value="{{ $activity->etudiant }}" />
                        <span class="input-group-text" id="basic-addon2">FCFA</span>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="expiry">Date début</label>
                                <input readonly type="date" class="form-control" id="dateStart" name="dateStart" value="{{ Carbon::parse($program->dateStart)->format('Y-m-d') }}" >
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="expiry">Date fin</label>
                                <input readonly type="date" class="form-control" id="dateEnd" name="dateEnd" value="{{ Carbon::parse($program->dateEnd)->format('Y-m-d') }}" >
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
                    @if($activity->image != "activity/")
                        <div class="card-body">
                            <div class="col-4">
                                <a><img src="{{ Storage::url("{$activity->image}") }}" alt="{{$activity->title}}" style="width:100%;">
                                </a>
                            </div>
                        </div>
                    @endif
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
<script src="{{ URL::asset('assets/js/pages/property_form.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<!------ ajoout de quill js pour edition de la description---------->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.9/highlight.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@gzzhanghao/quill-image-resize-module@3.0.6/image-resize.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="{{ URL::asset('/assets/blog/image-resize.min.js') }}"></script>
<script src="{{ URL::asset('/assets/blog/video-resize.min.js') }}"></script>

<!-- Initialisation de quill -->
<script type="text/javascript">
    //initialisation de l'editeur
    var options = {
        modules: {
            toolbar: [
                [{
                    'font': []
                }, {
                    'size': []
                }],
                ['bold', 'italic', 'underline', 'strike'],
                [{
                    'color': []
                }, {
                    'background': []
                }],
                [{
                    'script': 'super'
                }, {
                    'script': 'sub'
                }],
                [{
                    'header': '1'
                }, {
                    'header': '2'
                }, 'blockquote', 'code-block'],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }, {
                    'indent': '-1'
                }, {
                    'indent': '+1'
                }],
                ['direction', {
                    'align': []
                }],
                ['link', 'image', 'video', 'formula'],
                ['clean']
            ],
            imageResize: {
                modules: ['Resize', 'DisplaySize', 'Toolbar']
            },
            videoResize: {
                modules: ['Resize', 'DisplaySize', 'Toolbar']
            },
            syntax: true,
        },
        placeholder: 'Ecrivez ici...',
        theme: 'snow' // or 'bubble'
    };

    var quill = new Quill('#message', options);

    //a la sumissio  du formulmaire ob recupêre
    //le contenu de la div qui a le texte riche
    //et on met ce contenu dans l'input hidden
    var form = document.getElementById('form');
    form.onsubmit = function () {
        // Populate hidden form on submit
        var text = document.querySelector('input[name=content]');
        text.value = quill.root.innerHTML;

        //   console.log("Submitted", $(form).serialize(), $(form).serializeArray());

        // No back end to actually submit to!
        //   alert('Open the console to see the submit data!')
        return true;
    };
</script>
@endsection
