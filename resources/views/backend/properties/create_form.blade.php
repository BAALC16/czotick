@extends('layouts.master')
@section('title')
  @if($edit) {{ $property->title }} @else Ajouter {{ $propertyType->name }} @endif
@endsection
@section('css')
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Propri&eacute;t&eacute;s @endslot
@slot('title')@if($edit) {{ $property->title }} @else Ajouter {{ $propertyType->name }} @endif @endslot
@endcomponent
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">
                                            <i class="las {{$propertyType->icon}}"></i> D&eacute;tails {{ $propertyType->name }}
                                        </h4>
                                    </div><!-- end card header -->
                                </div>
                                <!-- end card -->
                                <div class="card">
                                    <form method="POST" @if($edit) action="{{ route('properties.update', $property->id) }}" @else action="{{ route('properties.store') }}" @endif enctype="multipart/form-data" class="form-xhr blockui">
                                    @csrf
                                    <input type="hidden" name="_method" @if($edit) value="patch" @else value="post" @endif />

                                    @if(!empty(request('continue')))
                                        <input type="hidden" name="continue" value="{{ request('continue') }}" />
                                    @endif

                                    <input type="hidden" name="propertyType" value="{{$propertyType->id}}">
                                    <div class="card-body form-steps">

                                        <div>
                                            <div>
                                                <div class="btn-group mb-3 mt-md-0" role="group" id="purpose_radio">
                                                    @foreach($transactions as $t)
                                                        <input type="radio" class="btn-check" id="transaction-{{$t->slug}}" name="purpose" value="{{$t->slug}}" @if($loop->first) checked @endif>
                                                        <label class="btn btn-outline-warning btn-lg" for="transaction-{{$t->slug}}"><i class="{{$t->icon}}"></i> {{$t->name}}</label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="fs-14 mb-3 text-muted">LOCALISATION</h5>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <select class="form-control" id="city" data-choices data-choices-groups data-placeholder="Commune" name="city">
                                                                <option value="">Commune</option>
                                                                <optgroup label="Grand Abidjan">
                                                                @foreach ($cities as $c)
                                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                                @endforeach
                                                                </optgroup>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="input-group mb-3">
                                                            <label class="input-group-text" for="address">Quartier</label>
                                                            <input type="text" class="form-control" id="project-address-input" name="address" @if($edit) value="{{ $property->address }}" @endif >
                                                        </div>
                                                    </div>
                                                @if ($propertyType->slug == "appartement")
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <div class="input-group mb-3">
                                                                <label class="input-group-text" for="project-floor-input">&Eacute;tage</label>
                                                                <input type="text" class="form-control" id="project-floor-input" name="floor" @if($edit) value="{{ $property->floor }}" @endif  >
                                                                <span class="input-group-text"> sur </span>
                                                                <input type="text" class="form-control" id="project-floor_max-input" name="floor_max" @if($edit) value="{{ $property->floor_max }}" @endif >
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                </div>
                                            </div>
                                            <div class="border mt-3 border-dashed"></div>
                                            <div class="mt-4">
                                            <h5 class="fs-14 mb-3 text-muted">D&Eacute;TAILS</h5>
                                                <div class="row">
                                                    <div class="col-lg-8 col-md-6">
                                                        <div class="input-group mb-3">
                                                            <label class="input-group-text" for="project-title-input">Nom de la Propri&eacute;t&eacute;</label>
                                                            <input type="text" class="form-control" id="project-title-input" name="title" maxlength="100" @if($edit) value="{{ $property->title }}" @endif >
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <div class="input-group mb-3">
                                                                <label class="input-group-text" for="project-area-input">Surface</label>
                                                                <input type="text" class="form-control" id="project-area-input" name="area" @if($edit) value="{{ $property->area }}" @endif >
                                                                <label class="input-group-text">m2</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                @if ($propertyType->slug == "villa")
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <select class="form-select" id="villa_type" data-placeholder="Type" name="villa_type">
                                                                <option value="">Type</option>
                                                                @foreach ($villaTypes as $v)
                                                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($propertyType->slug == "appartement" || $propertyType->slug == "villa")
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <select class="form-select" id="layoutType" data-placeholder="Type" name="layoutType">
                                                                <option value="">Nbre de Pi&egrave;ces</option>
                                                                @foreach ($layouts as $l)
                                                                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <div class="input-group mb-3">
                                                                <label class="input-group-text" for="project-bedroom-input">Nombre de Chambres</label>
                                                                <input type="text" class="form-control" id="project-bedroom-input" name="bedroom" @if($edit) value="{{ $property->bedroom }}" @endif  >
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($propertyType->slug == "appartement" || $propertyType->slug == "villa" || $propertyType->slug == "bureau" || $propertyType->slug == "magasin")
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <div class="input-group mb-3">
                                                                <label class="input-group-text" for="project-bathroom-input">Nombre de Salles de Bain</label>
                                                                <input type="text" class="form-control" id="project-bathroom-input" name="bathroom" @if($edit) value="{{ $property->bathroom }}" @endif >
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <div class="input-group mb-3">
                                                                <label class="input-group-text" for="project-price-input" id="price_label">Loyer</label>
                                                                <input type="text" class="form-control" id="project-price-input" name="price" @if($edit) value="{{ $property->price }}" @endif >
                                                                <label class="input-group-text" id="price_label1">FCFA / mois</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6" id="guarantee_block">
                                                        <div class="mb-3">
                                                            <div class="input-group mb-3">
                                                                <label class="input-group-text" for="project-guarantee-input">Caution</label>
                                                                <input type="text" class="form-control" id="project-guarantee-input" name="guarantee" @if($edit) value="{{ $property->guarantee }}" @endif >
                                                                <label class="input-group-text">FCFA</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6" id="key_money_block">
                                                        <div class="mb-3">
                                                            <div class="input-group mb-3">
                                                                <label class="input-group-text" for="project-key_money-input">Avance</label>
                                                                <input type="text" class="form-control" id="project-key_money-input" name="key_money" @if($edit) value="{{ $property->key_money }}" @endif >
                                                                <label class="input-group-text">FCFA</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                @if (count($features))
                                            <div class="border mt-3 border-dashed"></div>
                                            <div class="mt-4">
                                                <h5 class="fs-14 mb-3 text-muted">Options</h5>
                                                <div class="input-group mb-3">
                                                    <div class="btn-group mb-3 mt-md-0" role="group">
                                                    @foreach($features as $feature)
                                                        <input type="checkbox" class="btn-check" id="features-{{$feature->id}}" name="features[]" value="{{$feature->id}}">
                                                        <label class="btn btn-outline-primary" for="features-{{$feature->id}}"><i class="{{$feature->icon}}"></i> {{$feature->name}}</label>
                                                    @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                                @endif
                                            <div class="border mt-3 border-dashed"></div>
                                            <div class="mt-4">
                                                <div class="mb-3">
                                                    <label class="form-label" for="">Description</label>
                                                    <textarea id="description" name="description" rows="8">@if($edit){{ $property->description }}@endif</textarea>
                                                </div>
                                            </div>
                                            <div class="border mt-3 border-dashed"></div>
                                            <div class="mt-4">
                                                <h5 class="fs-14 mb-3 text-muted">IMAGES & VID&Eacute;OS</h5>
                                                <div>
                                                    <label class="form-label" for="">Images de la Propri&eacute;t&eacute;</label>
                                                    <input type="file" name="galleryimage[]" accept="image/*" id="gallery" class="filepond filepond-input-multiple" multiple data-allow-reorder="true" data-max-files="10" data-show-loader="true" data-allowed-file-extensions="jpg jpeg png gif svg" data-max-file-size="20MB" />
                                                </div>
                                                @if ($propertyType->name != "Terrain")
                                                <div class="mt-3">
                                                    <label class="form-label" for="">Plan</label>
                                                    <input type="file" name="floorplan" accept="image/*" id="plan" class="filepond" data-show-loader="true" data-allowed-file-extensions="jpg jpeg png gif svg" data-max-file-size="20MB" />
                                                </div>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                <button type="submit" class="btn btn btn-success btn-label right ms-auto"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Enregistrer</button>
                                            </div>
                                        </div>
                                        <!-- end tab content -->
                                    </div>
                                </form>
                                </div>

                            </div>
                            <!-- end col -->

                        </div>
                        <!-- end row -->

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
        <script src="{{ URL::asset('assets/libs/@ckeditor/@ckeditor.min.js') }}"></script>
        <!-- filepond js -->
        <script src="{{ URL::asset('assets/libs/filepond/filepond.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
        <script src="{{ URL::asset('assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
       @if($edit)
           <script>
               var images={!! json_encode($gallery) !!};
               var plans={!! json_encode($floor_plan) !!};
           </script>
           <script src="{{ URL::asset('assets/js/pages/property_form_edit.js') }}"></script>

       @else
           <script src="{{ URL::asset('assets/js/pages/property_form.js') }}"></script>
       @endif
        <script src="{{ URL::asset('assets/js/pages/form-wizard.init.js') }}"></script>
        <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
        @endsection
