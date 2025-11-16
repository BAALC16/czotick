@extends('layouts.master')
@section('title')
  @if($edit) {{ $rent->property->title }} @else Ajouter une location @endif
@endsection
@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Locations @endslot
@slot('title')Ajouter Location @endslot
@endcomponent
                        <form method="POST" @if($edit) action="{{ route('rents.update', ['rent' => $rent]) }}" @else action="{{ route('rents.store') }}" @endif class="form-xhr">
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
                                            <label class="input-group-text" for="project-price-input">Propriété</label>
                                            <select class="form-control form-select" aria-label="Default select example" name="property">
                                                @forelse ($properties as $property)
                                                    <option value="{{$property->id}}" @if($edit) @if($rent->property_id == $property->id) selected @endif @endif>{{$property->title}}</option>
                                                @empty
                                                    <option value="">Aucune donnée</option>
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="mb-3 input-group">
                                            <label class="input-group-text">Commune</label>
                                            <input class="form-control" name="city" value="{{$property->cityname->name}}" readonly/>
                                        </div>
                                        <div class="mb-3 input-group">
                                            <label class="input-group-text">Agent</label>
                                            <input class="form-control" id="agent" value="" readonly />
                                            <input type="hidden" class="form-control" name="agent" value="" />
                                        </div>
                                        <div class="mb-3 input-group">
                                            <label class="input-group-text">Locataire</label>
                                            <input class="form-control" id="user" value="" readonly />
                                            <input type="hidden" class="form-control" name="user" value="" />
                                        </div>
                                        <div class="mb-3 input-group">
                                            <label class="input-group-text">Loyer</label>
                                            <input class="form-control" name="amount" value="{{$property->price}}" readonly />
                                        </div>
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="startDay">Date début</label>
                                            <input type="date" class="form-control" id="startDay" name="startDay" @if($edit) value="{{Carbon::parse($rent->startDay)->format('Y-m-d')}}" @endif >
                                        </div>
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="endDay">Date fin</label>
                                            <input type="date" class="form-control" id="endDay" name="endDay" @if($edit) value="{{Carbon::parse($rent->endDay)->format('Y-m-d')}}" @endif >
                                        </div>

                                    </div>
                                    <!-- end card body -->
                                </div>

                                <div class="text-end mb-4">
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

            <script type="text/javascript">
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $(document).ready(function(){

                    function rent(property){
                        if (property) {
                            $.ajax({
                                url: "{{url('/backend/rents/getRent')}}",
                                type: "POST",
                                dataType: "json",
                                data: {"property": property},
                                success: function(data){
                                    console.log(data);
                                    $('#agent').val(data.agent);
                                    $('#user').val(data.user);
                                    $('input[name="agent"]').val(data.agent_id);
                                    $('input[name="user"]').val(data.user_id);
                                /*  $('select[name="sousCat"]').empty();
                                        jQuery.each(data, function(index, value) {
                                        $('select[name="sousCat"]').append('<option value="'+value.id+'">'+value.name+'</option>');
                                    }); */
                                }
                            });
                        }else {
                            //$('select[name="sousCat"]').empty();
                        }
                    }

                    var property;
                    property = $('select[name="property"]').val();
                    rent(property);

                    $('select[name="property"]').on('change',function(){
                        property= $(this).val();
                        rent(property);
                    });

                });
            </script>

        @endsection

