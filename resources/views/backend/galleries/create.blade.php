@extends('layouts.master')
@section('title')
  @if($edit) Editer images @else Ajouter images @endif
@endsection
@section('css')
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Galérie @endslot
@slot('title')Choisir une catégorie @endslot
@endcomponent
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Catégories</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="text-center mb-3 mt-3">
                        <div class="btn-group-lg justify-content-center" role="group" aria-label="Basic example">
                            @foreach($categories as $category)
                                @if($edit)
                                    <a href="{{ route('galleries.edit.category',['category' => $category->slug]) }}" class="btn btn-info btn-label waves-effect waves-light"><i class="las {{$category->icon}}"></i> {{$category->label}}</a>
                                @else
                                <a href="{{ route('galleries.create.category', ['category' => $category->slug]) }}" class="btn btn-info btn-label waves-effect waves-light">{{$category->label}}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div>
            <!-- end card -->
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
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
