@extends('layouts.master')
@section('title') Requ&ecirc;te @endsection
@section('css')
    <link href="{{ URL::asset('assets/libs/multi.js/multi.js.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/libs/@tarekraafat/@tarekraafat.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/glightbox/glightbox.min.css') }}" type="text/css" />
    <link href="/vendors/waitMe/waitMe.min.css" rel="stylesheet">
@endsection
@section('content')
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">   
        <div class="chat-leftsidebar" style="width:1px; min-width: 1px; !important"></div>        
         <!-- Start User chat -->
        <div class="user-chat w-100 overflow-hidden">

            <div class="chat-content d-lg-flex">
                <!-- start chat conversation section -->
                <div class="w-100 overflow-hidden position-relative">
                    <!-- conversation user -->
                    <div class="position-relative">
                        <div class="p-3 user-chat-topbar">
                            <div class="row align-items-center">
                                <div class="col-sm-4 col-8">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 d-block d-lg-none me-3">
                                            <a href="javascript: void(0);" class="user-chat-remove fs-18 p-1"><i class="ri-arrow-left-s-line align-bottom"></i></a>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 chat-user-img online user-own-img align-self-center me-3 ms-0">
                                                    <img src="{{$other->photo_url}}" class="rounded-circle avatar-xs" alt="">
                                                    <span class="user-status"></span>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h5 class="text-truncate mb-0 fs-16">
                                                    {{$other->full_name}}
                                                    </h5>
                                                    <p class="text-truncate text-muted fs-14 mb-0 userStatus">
                                                        <small>
                                                                            
                                                        @can('view', $inquiry->property)
                                                            <a href="{{ route('properties.show', $inquiry->property) }}">{{ $inquiry->property->title }}</a>
                                                        @else
                                                        <a class="font-weight-normal" target="_blank" href="{{ route('public.property', ['property' => $inquiry->property, 'address' => str_slug($inquiry->property->fullAddress()), 'title' => $inquiry->property->slug]) }}">{{ $inquiry->property->title }}</a>
                                                            
                                                        @endcan | {{ Carbon\Carbon::parse($inquiry->created_at)->isoFormat('dddd DD MMMM YYYY') }} 
                                    <span class="badge badge-soft-{{$inquiry->status->color}}">{{$inquiry->status->label}}</span>
                                                        </small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-8 col-4">
                                    <ul class="list-inline user-chat-nav text-end mb-0">
                                    @switch($inquiry->status_code)
                                        @case("DEM_SOUMISE")
                                        @case("DEM_REJETEE")
                                            @can('validate', App\Models\Inquiry::class)
                                        <li class="list-inline-item m-0">
                                            <a href="{{ route('inquiries.switch', ['inquiry' => $inquiry, 'action' => 'accept']) }}" class="btn btn-success btn-label waves-effect waves-light btn-sm">
                                                <i class="ri-youtube-line label-icon align-middle fs-16 me-2"></i> Accepter
                                            </a>
                                        </li>
                                            @endcan
                                        @break
                                        @case("DEM_ACCEPTEE")
                                            @can('reject', App\Models\Inquiry::class)
                                        <li class="list-inline-item m-0">
                                            <a href="{{ route('inquiries.switch', ['inquiry' => $inquiry, 'action' => 'reject']) }}" class="btn btn-warning btn-label waves-effect waves-light btn-sm">
                                                <i class="ri-reply-line label-icon align-middle fs-16 me-2"></i> Rejeter
                                            </a>
                                        </li>
                                            @endcan
                                        @break
                                    @endswitch
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <!-- end chat user head -->

                        <div class="position-relative" id="users-chat">
                            <div class="chat-conversation p-3 p-lg-4 " id="chat-conversation" data-simplebar>
                                <ul class="list-unstyled chat-conversation-list" id="users-conversation">

                                <li class="mb-5 mt-5" style="display:block">
                                    <div class="alert alert-warning alert-label-icon rounded-label" role="alert">
                                        <i class="ri-alert-line label-icon"></i><strong>Admin</strong> - 
                                        Veuillez ne pas communiquer &agrave; l'Agent des d&eacute;tails personnels.
                                        <small class="text-muted time">{{Carbon\Carbon::parse($inquiry->created_at)->isoFormat("DD MMM YYYY \à HH:mm")}}</small>
                                    </div>
                                </li>
                                @foreach ($inquiry->comments as $k => $comment)

                                @if ($comment->user->getIsAdminAttribute() && $inquiry->agent->isNot($comment->user))
                                <li class="mb-3" style="display:block">
                                    <!-- Warning Alert -->
                                    <div class="alert alert-warning alert-label-icon rounded-label" role="alert">
                                        <i class="ri-alert-line label-icon"></i><strong>Admin</strong> - 
                                        @if($comment->text)
                                            {!! nl2br(strip_tags($comment->text)) !!}
                                        @endif
                                        <small class="text-muted time">{{Carbon\Carbon::parse($comment->created_at)->isoFormat("DD MMM YYYY \à HH:mm")}}</small>
                                    </div>
                                </li>
                                @else
                                <li class="chat-list @if (auth()->user()->is($comment->user)) right @else left @endif">
                                    <div class="conversation-list">
                                        @if (auth()->user()->isNot($comment->user)) 
                                        <div class="chat-avatar">
                                            <img src="{{ $comment->user->photo_url }}" alt="">
                                        </div>
                                        @endif
                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                    <div class="ctext-wrap-content">
                                                        <p class="mb-0 ctext-content">
                                                        @if($comment->text)
                                                            {!! nl2br(strip_tags($comment->text)) !!}
                                                        @endif
                                                        </p>
                                                    </div>
                                        @if($comment->pieces_jointes->isNotEmpty())
                                            </div>
                                            <div class="ctext-wrap">
                                                <div class="message-img mb-0">
                                                @foreach ($comment->pieces_jointes as $pj)
                                                @if (Storage::disk('public')->exists($pj->chemin))
                                                    <div class="message-img-list">
                                                        <div>
                                                            <a class="image-popup d-inline-block" href="{{Storage::url($pj->chemin)}}">
                                                                <img src="{{Storage::url($pj->chemin)}}" alt="" class="rounded border">
                                                            </a>
                                                        </div>
                                                        <div class="message-img-link">
                                                            <ul class="list-inline mb-0">
                                                                <li class="list-inline-item dropdown">
                                                                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="ri-more-fill"></i>
                                                                    </a>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item" href="{{ route('download', ['file' => $pj->chemin, 'name' => strip_tags($pj->nom)]) }}" download=""><i class="ri-download-2-line me-2 text-muted align-bottom"></i>T&eacute;l&eacute;charger</a>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                                @endforeach
                                            </div>
                                        @endif
                                            </div>
                                            <div class="conversation-name">
                                                <small class="text-muted time">{{Carbon\Carbon::parse($comment->created_at)->isoFormat("DD MMM YYYY \à HH:mm")}}</small>
                                                @if ($comment->read)
                                                <span class="text-success check-message-icon"><i class="ri-check-double-line align-bottom"></i></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                                <!-- chat-list -->    
                                @endforeach
                                </ul>
                                <a href="#comments"></a>
                                <!-- end chat-conversation-list -->
                            </div>
                        </div>

                        <!-- end chat-conversation -->

                        <div class="chat-input-section p-3 p-lg-4">

                            <form method="post" class="blockui" action="{{route('inquiries.comment', $inquiry)}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-0 align-items-center">

                                    <div class="col">
                                        <div class="chat-input-feedback">
                                            Votre Message
                                        </div>
                                        <input type="text" class="form-control chat-input bg-light border-light" id="chat-input" placeholder="Votre Message..." autocomplete="off" name="text">
                                    </div>
                                    <div class="col-auto">
                                        <div class="chat-input-links ms-2">
                                            <div class="links-list-item">
                                                <input type="file" name="fichiers[]" class="d-none" id="comment-files" multiple />
                                                <button onclick="document.getElementById('comment-files').click();" class="btn btn-ghost-secondary btn-icon waves-effect me-1" type="button" id="btn-comment-files"><i class="ri-attachment-line fs-16"></i></button>
                                                <button type="submit" class="btn btn-success chat-send waves-effect waves-light">
                                                    <i class="ri-send-plane-2-fill align-bottom"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!--end row-->
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
    <script src="{{ URL::asset('assets/libs/glightbox/glightbox.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/@tarekraafat/@tarekraafat.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/chat.init.js') }}"></script>
@endsection
