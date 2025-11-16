@extends('layouts.master')
@section('title')
  @if($edit) {{ $feature->name }} @else Ajouter une option @endif
@endsection
@section('css')
<link href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet">
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Options @endslot
@slot('title')Ajouter Options @endslot
@endcomponent

<form method="POST" @if($edit) action="{{ route('features.update', $feature) }}" @else action="{{ route('features.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
@csrf
<input type="hidden" name="_method" @if($edit) value="patch" @else value="post" @endif />

@if(!empty(request('continue')))
    <input type="hidden" name="continue" value="{{ request('continue') }}" />
@endif
<div class="row icon-demo-content">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="project-title-input">Titre de l'Option</label>
                    <input type="text" class="form-control" id="project-title-input" name="name" @if($edit) value="{{ $feature->name }}" @endif >
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text" for="project-icon-input">Ic&ocirc;ne</label>
                    <input type="text" class="form-control" id="project-icon-input" name="icon" @if($edit) value="{{ $feature->icon }}" @endif >
                </div>
                <div class="input-group">
                    <p class="text-muted fw-medium">Type(s) de bien</p>

                </div>
                <div class="input-group mb-3">
                    @foreach($propertyTypes as $p)
                        <input type="checkbox" class="btn-check" id="property-type-{{$p->id}}" name="propertyTypes[]" value="{{$p->id}}">
                        <label class="btn btn-outline-primary" for="property-type-{{$p->id}}"><i class="las {{$p->icon}}"></i>{{$p->name}}</label>
                    @endforeach
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
<script src="/js/theme.js"></script>
<script src="/vendors/config.js"></script>
<script src="/js/backoffice.js"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
