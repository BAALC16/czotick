@extends('layouts.master')
@section('title')
@endsection
@section('css')
@livewireStyles()
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
<link rel="stylesheet"
  href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="/public/css/lity.min.css">

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1')
Articles
@endslot
@slot('title')
Ajouter Articles
@endslot
@endcomponent

{{-- ajout du script livewire --}}
<livewire:gallerie/>
{{-- fin ajout du script livewire --}}
@endsection
@section('script')
@livewireScripts()
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
<script src="/public/js/lity.min.js"></script>

<script type="text/JavaScript">
        function readURL(input) {
            if (input.files && input.files[0]) {

                var reader = new FileReader();

                reader.onload = function(e) {
                    $('.image-upload-wrap').hide();

                    $('.file-upload-image').attr('src', e.target.result);
                    $('.file-upload-content').show();

                    $('.image-title').html(input.files[0].name);
                };

                reader.readAsDataURL(input.files[0]);

            } else {
                removeUpload();
            }
        }

        function removeUpload() {
            $('.file-upload-input').replaceWith($('.file-upload-input').clone());
            $('.file-upload-content').hide();
            $('.image-upload-wrap').show();
        }
        $('.image-upload-wrap').bind('dragover', function() {
            $('.image-upload-wrap').addClass('image-dropping');
        });
        $('.image-upload-wrap').bind('dragleave', function() {
            $('.image-upload-wrap').removeClass('image-dropping');
        });
    </script>

@endsection