@extends('layouts.master')
@section('title')
  <!-- @if($edit) {{ $property->title }} @else Ajouter une Propri&eacute;t&eacute; @endif -->
@endsection
@section('css')
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Points Cr&eacute;dits @endslot
@slot('title') Point initial @endslot
@endcomponent
    <div class="row">
        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-body p-0">
                    <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0">
                        <div class="col">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Conversion</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-0"><span>{{ $creditPoint->point }} Point Crédit = {{ $creditPoint->amount }} FCFA</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Point initial</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-coins-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h4 class="mb-0"><span>{{ $initialPoint->point }}</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Changer le point initial</h5>
                                <form method="POST" action="{{ route('credit-points.update', ['credit_point' => $initialPoint]) }}" class="form-xhr">
                                    @csrf
                                    <input type="hidden" name="_method" value="patch" />
                                    <div class="d-flex gap-3 mt-3">
                                        <div>
                                            <input type="number" class="form-control" name="initialPoint" value="{{ $initialPoint->point }}">
                                        </div>
                                        <div class="flex-shrink-0">
                                            <input class="btn btn-success add-btn" id="credit-point" type="submit" value="Valider">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div><!-- end col -->
                    </div><!-- end row -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
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
