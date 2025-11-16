@extends('layouts.master')
@section('title') 
  @if($edit) {{ $promotion->label }} @else Ajouter une promotion @endif
@endsection
@section('css')
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Promotions @endslot
@slot('title')Ajouter Promotion @endslot
@endcomponent
                        <form method="POST" @if($edit) action="{{ route('credit-points-promotions.update', ['credit_points_promotion' => $promotion]) }}" @else action="{{ route('credit-points-promotions.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
                        @csrf
                        <input type="hidden" name="_method" @if($edit) value="patch" @else value="post" @endif />

                        @if(!empty(request('continue')))
                            <input type="hidden" name="continue" value="{{ request('continue') }}" />
                        @endif
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="project-title-input">Titre de la Promotion</label>
                                            <input type="text" class="form-control" id="project-title-input" name="title" @if($edit) value="{{ $promotion->title }}" @endif >
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="">Description</label>
                                            <textarea id="description" name="description" rows="8">@if($edit){{ $promotion->description }} @endif</textarea>
                                        </div>
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="credit-points">Points Crédit</label>
                                            <input type="number" class="form-control" id="credit-points" name="point" @if($edit) value="{{ $promotion->creditPoints->point }}" @endif >
                                        </div>
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="amount">Montant</label>
                                            <input type="text" class="form-control" id="amount" name="amount" @if($edit) value="{{ $promotion->creditPoints->amount }}" @endif >
                                            <label class="input-group-text">FCFA</label>
                                        </div>
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="expiry">Date d'expiration</label>
                                            <input type="date" class="form-control" id="expiry" name="expiry" @if($edit) value="{{ Carbon::parse($promotion->expiry)->format('Y-m-d') }}" @endif >
                                        </div>
                                    </div>
                                    <!-- end card body -->
                                </div>
                                <!-- end card -->

                                <div class="text-end mb-4">
                                    <button type="submit" class="btn btn-success w-sm">Enregistrer</button>
                                </div>
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
       @if($edit)
           <script src="{{ URL::asset('assets/js/pages/property_form_edit.js') }}"></script>
       @else
           <script src="{{ URL::asset('assets/js/pages/property_form.js') }}"></script>
       @endif
        <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
        @endsection
