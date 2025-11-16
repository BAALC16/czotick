@extends('layouts.master')
@section('title') 
  @if($edit) {{ $service->label }} @else Ajouter un service @endif
@endsection
@section('css')
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Services @endslot
@slot('title')Ajouter Service @endslot
@endcomponent
                        <form method="POST" @if($edit) action="{{ route('services.update', $service) }}" @else action="{{ route('services.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
                        @csrf
                        <input type="hidden" name="_method" @if($edit) value="patch" @else value="post" @endif />

                        @if(!empty(request('continue')))
                            <input type="hidden" name="continue" value="{{ request('continue') }}" />
                        @endif
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="project-title-input">Titre du Service</label>
                                            <input type="text" class="form-control" id="project-title-input" name="label" @if($edit) value="{{ $service->label }}" @endif  required>
                                        </div>
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="project-icon-input">Ic&ocirc;ne</label>
                                            <input type="text" class="form-control" id="project-icon-input" name="icon" @if($edit) value="{{ $service->icon }}" @endif required>
                                        </div>
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="project-price-input">Prix</label>
                                            <input type="text" class="form-control" id="project-price-input" name="prix" @if($edit) value="{{ $service->prix }}" @endif required>
                                            <label class="input-group-text">FCFA</label>
                                        </div>
                                        <div class="mb-3">
                                          <input class="form-check-input" type="checkbox" @if(!$edit || ($edit && $service->actif)) checked @endif  name="actif" value="1" id="defaultCheck1">
                                          <label class="form-check-label" for="defaultCheck1"> Publier</label>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="">Description</label>
                                            <textarea id="description" name="description" rows="8">@if($edit){{ $service->description }}@endif</textarea>
                                        </div>

                                    </div>
                                    <!-- end card body -->
                                </div>
                                <!-- end card -->

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Image</h5>
                                    </div>
                                    <div class="card-body">
                                        <input type="file" name="image_file" accept="image/*"   class="filepond" data-show-loader="true" data-allowed-file-extensions="jpg jpeg png gif svg" data-max-file-size="20MB" />
                                    </div>
                                <!-- end card -->
                                <div class="text-end mb-4">
                                    <button type="submit" class="btn btn-success w-sm">Enregistrer</button>
                                </div>
                            </div>
                            <!-- end col -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Attributs</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">Informations &agrave; fournir &agrave; la demande du service.</p>
                                        <div id="repeater">
                                            <div data-repeater-list="attributs">
                                            @if($edit && $service->attributs->isNotEmpty())
                                                @foreach ($service->attributs as $item)
                                                <div data-repeater-item="attributs_item">
                                                    <div class="row">
                                                        <div class="col-10">
                                                            <div class="mb-3 input-group">
                                                                <label class="input-group-text">Libell&eacute;</label>
                                                                <input class="form-control" name="label" value="{{ $item->label }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-2">
                                                            <div class="mb-3 d-flex">
                                                                <div class="flex-column">
                                                                    <a href="javascript:;" data-repeater-delete class="ml-3 mt-7 btn btn-outline btn-outline-danger">
                                                                        <i class="ri-delete-bin-5-fill fs-12"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="input-group mb-3">
                                                            <label class="input-group-text">Type de champ</label>
                                                            <select class="form-select" name="type_champ">
                                                                @foreach (App\Models\Attribut::types_champ as $key => $value)
                                                                  <option value="{{ $key }}" @if($item->type_champ == $key) selected @endif>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="input-group mb-3">
                                                            <label class="input-group-text">Description</label>
                                                            <input class="form-control" name="description" value="{{ $item->description }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                                <div data-repeater-item="attributs_item">
                                                    <div class="row">
                                                        <div class="col-10">
                                                            <div class="mb-3 input-group">
                                                                <label class="input-group-text">Libell&eacute;</label>
                                                                <input class="form-control" name="label" />
                                                            </div>
                                                        </div>
                                                        <div class="col-2">
                                                            <div class="mb-3 d-flex">
                                                                <div class="flex-column">
                                                                    <a href="javascript:;" data-repeater-delete class="ml-3 mt-7 btn btn-outline btn-outline-danger">
                                                                        <i class="ri-delete-bin-5-fill fs-12"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="input-group mb-3">
                                                            <label class="input-group-text">Type de champ</label>
                                                            <select class="form-select" name="type_champ">
                                                                @foreach (App\Models\Attribut::types_champ as $key => $value)
                                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="input-group mb-3">
                                                            <label class="input-group-text">Description</label>
                                                            <input class="form-control" name="description" />
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            </div>
                                            <div>
                                                <a href="javascript:;" data-repeater-create class="btn btn-outline btn-outline-primary">
                                                    <i class="fal fa-plus"></i> Ajouter un attribute
                                                </a>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- end card body -->
                                </div>
                                <!-- end card -->

                                
                            </div>
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
        <script src="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/pages/property_form.js') }}"></script>
        <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
        @endsection
