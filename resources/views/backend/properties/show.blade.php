@extends('layouts.master')
@section('title') D&eacute;tails du Bien @endsection
@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/glightbox/glightbox.min.css') }}" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Gestion Immobili&egrave;re @endslot
        @slot('title') D&eacute;tails du Bien @endslot
    @endcomponent
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#property-details" role="tab">
                                        D&eacute;tails
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#property-inquiries" role="tab">
                                        Requ&ecirc;tes <span class="badge badge-soft-danger align-middle rounded-pill ms-1">{{ count($allInquiries) }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#property-ratings" role="tab">
                                        Retours Client
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content text-muted">
                        <div class="tab-pane fade show active" id="property-details" role="tabpanel">
                            <div class="row gx-lg-5">
                                <div class="col-xl-3">
                                    <div class="product-img-slider sticky-side-div">
                                        <div class="swiper product-thumbnail-slider p-2 rounded bg-light">
                                            <div class="swiper-wrapper">
                                            @if (!($property->gallery->isEmpty()))
                                                @foreach($property->gallery as $gallery)
                                                @if (Storage::disk('public')->exists('property/gallery/'.$gallery->name))
                                                <div class="swiper-slide">
                                                    <a class="image-popup" href="{{Storage::url('property/gallery/'.$gallery->name)}}">
                                                    <img src="{{Storage::url('property/gallery/'.$gallery->name)}}" alt="{{$property->title}}" class="img-fluid d-block"/>
                                                </a>
                                                </div>
                                                @endif
                                                @endforeach
                                            @endif
                                            @if (!empty($property->floor_plan) && Storage::disk('public')->exists('property/floor/'.$property->floor_plan))
                                                <div class="swiper-slide">
                                                    <a class="image-popup" href="{{Storage::url('property/floor/'.$property->floor_plan)}}">
                                                    <img src="{{Storage::url('property/floor/'.$property->floor_plan)}}" alt="{{$property->title}}" class="img-fluid d-block" data-gtf-mfp="true"/>
                                                </a>
                                                </div>
                                            @endif
                                            </div>
                                            <div class="swiper-button-next"></div>
                                            <div class="swiper-button-prev"></div>
                                        </div>
                                        <!-- end swiper thumbnail slide -->
                                        <div class="swiper product-nav-slider mt-2">
                                            <div class="swiper-wrapper">
                                            @if (!($property->gallery->isEmpty()))
                                                @foreach($property->gallery as $gallery)
                                                @if (Storage::disk('public')->exists('property/gallery/'.$gallery->name))
                                                <div class="swiper-slide">
                                                    <img src="{{Storage::url('property/gallery/'.$gallery->name)}}" alt="{{$property->title}}"
                                                        class="img-fluid d-block" />
                                                </div>
                                                @endif
                                                @endforeach
                                            @endif
                                            @if (!empty($property->floor_plan) && Storage::disk('public')->exists('property/floor/'.$property->floor_plan))
                                                <div class="swiper-slide">
                                                    <img src="{{Storage::url('property/floor/'.$property->floor_plan)}}" alt="{{$property->title}}"
                                                        class="img-fluid d-block" />
                                                </div>
                                            @endif
                                            </div>
                                        </div>
                                        <!-- end swiper nav slide -->
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-xl-8">
                                    <div class="mt-xl-0 mt-5">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <h4>{{ $property->title }}@if ($property->layoutType) - {{ $property->layoutType->name }} @endif</h4>
                                                <div class="hstack gap-3 flex-wrap icon-demo-content">
                                                    <div><a href="#" class="text-primary d-block">{{ $property->fullAddress() }}</a></div>
                                                    <div class="vr"></div>
                                                    <div class="text-muted"><span class="text-body fw-medium">{{ $property->area }} m&sup2;</span>
                                                    </div>
                                                    @if ($property->floor > -1)
                                                    <div class="vr"></div>
                                                    <div class="text-muted"><i class="las la-layer-group"></i><span class="text-body fw-medium">{{ $property->floor }} / {{ $property->floor_max }}</span>
                                                    </div>
                                                    @endif
                                                    <div class="vr"></div>
                                                    <div class="text-muted"><i class="las la-bed"></i><span class="text-body fw-medium">{{ $property->bedroom }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div class="text-muted"><i class="las la-bath"></i><span class="text-body fw-medium">{{ $property->bathroom }}</span>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div class="text-muted">Publi&eacute; le : <span class="text-body fw-medium">{{ $property->dateCreated() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div>
                                                @can('validate', App\Models\Property::class)
                                                @if ($property->published)
                                                    <a href="{{ route('property.switch', ['property' => $property, 'action' => 'reject']) }}" class="btn btn-danger"><i class="las la-pause"></i></a>
                                                @else
                                                    <a href="{{ route('property.switch', ['property' => $property, 'action' => 'accept']) }}" class="btn btn-success"><i class="las la-play"></i></a>
                                                @endif
                                                @endcan
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
                                            <div class="text-muted fs-16">
                                                <span class="mdi mdi-star text-warning"></span>
                                                <span class="mdi mdi-star text-warning"></span>
                                                <span class="mdi mdi-star text-warning"></span>
                                                <span class="mdi mdi-star text-warning"></span>
                                                <span class="mdi mdi-star text-warning"></span>
                                            </div>
                                            <div class="text-muted">( {{ number_format($total,0) }} Retour Client )</div>
                                        </div>

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Description :</h5>
                                            <p>{!! $property->description !!}</p>
                                        </div>

                                        @if (!empty($property->nearby))
                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Environnement :</h5>
                                            <p>{!! $property->nearby !!}</p>
                                        </div>
                                        @endif

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Options :</h5>
                                            <div class="btn-group mb-3 mt-md-0" role="group">
                                            @foreach($property->features()->get() as $feature)
                                                <button type="button" class="btn btn-soft-primary waves-effect waves-light" id="features-{{$feature->id}}"><i class="{{$feature->icon}}"></i> {{$feature->name}}</button>
                                            @endforeach
                                            </div>
                                        </div>
                                        <!-- end card body -->
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="property-inquiries" role="tabpanel">
                            <div class="row gx-lg-5">
                                <div class="col-xl-3">
                                    <div class="product-img-slider">
                                    @if (!($property->gallery->isEmpty()))
                                        @foreach($property->gallery as $gallery)
                                        @if (Storage::disk('public')->exists('property/gallery/'.$gallery->name))
                                            <img src="{{Storage::url('property/gallery/'.$gallery->name)}}" alt="{{$property->title}}" class="img-fluid d-block"/>
                                            @break
                                        @endif
                                        @endforeach
                                    @endif
                                    </div>
                                </div>
                                <div class="col-xl-8">
                                    <div class="mt-xl-0 mt-5">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <h4>{{ $property->title }}@if ($property->layoutType) - {{ $property->layoutType->name }} @endif</h4>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div>
                                                @can('validate', App\Models\Property::class)
                                                @if ($property->published)
                                                    <a href="{{ route('property.switch', ['property' => $property, 'action' => 'reject']) }}" class="btn btn-danger"><i class="las la-pause"></i></a>
                                                @else
                                                    <a href="{{ route('property.switch', ['property' => $property, 'action' => 'accept']) }}" class="btn btn-success"><i class="las la-play"></i></a>
                                                @endif
                                                @endcan
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <ul class="nav nav-tabs mb-3 mt-3" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#inquiry-recent" role="tab">
                                                        <i class="ri-chat-new-line align-middle me-1"></i>R&eacute;cents <span class="badge bg-success rounded-circle">{{ count($recentInquiries)}}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link align-middle" data-bs-toggle="tab" href="#inquiry-all" role="tab">
                                                        <i class="ri-chat-new-line align-middle me-1"></i>Tous <span class="badge bg-success rounded-circle">{{ count($allInquiries)}}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link align-middle" data-bs-toggle="tab" href="#inquiry-shortlist" role="tab">
                                                        <i class="ri-star-line align-middle me-1"></i>Shortlist <span class="badge bg-success rounded-circle">{{ count($shortlistInquiries)}}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link align-middle" data-bs-toggle="tab" href="#inquiry-archived" role="tab">
                                                        <i class=" ri-inbox-archive-line align-middle me-1"></i>Archiv&eacute;s <span class="badge bg-success rounded-circle">{{ count($archivedInquiries)}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content text-muted">
                                                <div class="tab-pane fade show active" id="inquiry-recent" role="tabpanel">
                                                    <div class="table-responsive table-card mb-1 mt-3" style="margin-right:0px; margin-left: 0px;">
                                                        <table class="table table-nowrap align-middle" id="orderTable">
                                                            <thead class="text-muted table-light">
                                                                <tr class="text-uppercase">
                                                                    <th class="sort" data-sort="user">Client</th>
                                                                    <th class="sort" data-sort="status">Statut</th>
                                                                    <th class="sort" data-sort="date">Date</th>
                                                                    <th class="sort" data-sort="city"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="list form-check-all">
                                                            @forelse ($recentInquiries->sortByDesc('created_at') as $row)
                                                                <tr>
                                                                    <td class="amount">
                                                                    @can('view', $row->user)
                                                                        <a class="font-weight-normal" href="{{route('users.show', $row->user)}}">{{$row->user->full_name}}</a>
                                                                    @else
                                                                        {{$row->user->full_name}}
                                                                    @endcan
                                                                    </td>
                                                                    <td class="status">
                                                                        <span class="badge badge-soft-{{$row->status->color}}">{{$row->status->label}}</span>
                                                                    </td>
                                                                    <td class="date">
                                                                        {{ Carbon::parse($row->created_at)->isoFormat("DD MMM YYYY") }}
                                                                    </td>
                                                                    <td>
                                                                        <ul class="list-inline hstack gap-2 mb-0">
                                                                            @can('view', $row)
                                                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Chat">
                                                                                    <a href="{{route('inquiries.show', $row)}}" class="text-info d-inline-block">
                                                                                        <i class="ri-message-3-line fs-16"></i>
                                                                                    </a>
                                                                                </li>
                                                                            @endcan
                                                                            @switch($row->status_code)
                                                                                @case("DEM_REJETEE")
                                                                                    @can('validate', App\Models\Inquiry::class)
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'accept']) }}" class="text-primary d-inline-block">
                                                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                                @break
                                                                                @case("DEM_ACCEPTEE")
                                                                                    @can('reject', App\Models\Inquiry::class)
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                                                <i class="ri-close-fill label-icon align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                                @break
                                                                                @default
                                                                                    @can('validate', App\Models\Inquiry::class)
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'accept']) }}" class="text-success d-inline-block">
                                                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                                                <i class="ri-close-fill align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                            @endswitch
                                                                            @can('validate', App\Models\Inquiry::class)
                                                                                @switch($row->marker)
                                                                                    @case(1)
                                                                                        @can('validate', App\Models\Inquiry::class)
                                                                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Archiver">
                                                                                                <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'archive']) }}" class="text-warning d-inline-block">
                                                                                                    <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                    @break
                                                                                    @case(2)
                                                                                        @can('validate', App\Models\Inquiry::class)
                                                                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Selectionner">
                                                                                                <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'shortList']) }}" class="text-primary d-inline-block">
                                                                                                    <i class="ri-star-line align-middle fs-16"></i>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                    @break
                                                                                    @default
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Selectionner">
                                                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'shortList']) }}" class="text-primary d-inline-block">
                                                                                                <i class="ri-star-line align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Archiver">
                                                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'archive']) }}" class="text-warning d-inline-block">
                                                                                                <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                @endswitch
                                                                            @endcan
                                                                        </ul>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                              <tr>
                                                                <td colspan="4" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
                                                              </tr>
                                                            @endforelse

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="inquiry-all" role="tabpanel">
                                                    <div class="table-responsive table-card mb-1 mt-3" style="margin-right:0px; margin-left: 0px;">
                                                        <table class="table table-nowrap align-middle" id="orderTable">
                                                            <thead class="text-muted table-light">
                                                                <tr class="text-uppercase">
                                                                    <th class="sort" data-sort="user">Client</th>
                                                                    <th class="sort" data-sort="status">Statut</th>
                                                                    <th class="sort" data-sort="date">Date</th>
                                                                    <th class="sort" data-sort="city"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="list form-check-all">
                                                            @forelse ($allInquiries->sortByDesc('created_at') as $row)
                                                                <tr>
                                                                    <td class="amount">
                                                                    @can('view', $row->user)
                                                                        <a class="font-weight-normal" href="{{route('users.show', $row->user)}}">{{$row->user->full_name}}</a>
                                                                    @else
                                                                        {{$row->user->full_name}}
                                                                    @endcan
                                                                    </td>
                                                                    <td class="status">
                                                                        <span class="badge badge-soft-{{$row->status->color}}">{{$row->status->label}}</span>
                                                                    </td>
                                                                    <td class="date">
                                                                        {{ Carbon::parse($row->created_at)->isoFormat("DD MMM YYYY") }}
                                                                    </td>
                                                                    <td>
                                                                        <ul class="list-inline hstack gap-2 mb-0">
                                                                            @can('view', $row)
                                                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Chat">
                                                                                    <a href="{{route('inquiries.show', $row)}}" class="text-info d-inline-block">
                                                                                        <i class="ri-message-3-line fs-16"></i>
                                                                                    </a>
                                                                                </li>
                                                                            @endcan
                                                                            @switch($row->status_code)
                                                                                @case("DEM_REJETEE")
                                                                                    @can('validate', App\Models\Inquiry::class)
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'accept']) }}" class="text-primary d-inline-block">
                                                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                                @break
                                                                                @case("DEM_ACCEPTEE")
                                                                                    @can('reject', App\Models\Inquiry::class)
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                                                <i class="ri-close-fill label-icon align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                                @break
                                                                                @default
                                                                                    @can('validate', App\Models\Inquiry::class)
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'accept']) }}" class="text-success d-inline-block">
                                                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                                                <i class="ri-close-fill align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                            @endswitch
                                                                            @can('validate', App\Models\Inquiry::class)
                                                                                @switch($row->marker)
                                                                                    @case(1)
                                                                                        @can('validate', App\Models\Inquiry::class)
                                                                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Archiver">
                                                                                                <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'archive']) }}" class="text-warning d-inline-block">
                                                                                                    <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                    @break
                                                                                    @case(2)
                                                                                        @can('validate', App\Models\Inquiry::class)
                                                                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Selectionner">
                                                                                                <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'shortList']) }}" class="text-primary d-inline-block">
                                                                                                    <i class="ri-star-line align-middle fs-16"></i>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                    @break
                                                                                    @default
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Selectionner">
                                                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'shortList']) }}" class="text-primary d-inline-block">
                                                                                                <i class="ri-star-line align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Archiver">
                                                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'archive']) }}" class="text-warning d-inline-block">
                                                                                                <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                @endswitch
                                                                            @endcan
                                                                        </ul>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                              <tr>
                                                                <td colspan="4" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
                                                              </tr>
                                                            @endforelse

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="inquiry-shortlist" role="tabpanel">
                                                    <div class="table-responsive table-card mb-1 mt-3" style="margin-right:0px; margin-left: 0px;">
                                                        <table class="table table-nowrap align-middle" id="orderTable">
                                                            <thead class="text-muted table-light">
                                                                <tr class="text-uppercase">
                                                                    <th class="sort" data-sort="user">Client</th>
                                                                    <th class="sort" data-sort="status">Statut</th>
                                                                    <th class="sort" data-sort="date">Date</th>
                                                                    <th class="sort" data-sort="city"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="list form-check-all">
                                                            @forelse ($shortlistInquiries->sortByDesc('created_at') as $row)
                                                                <tr>
                                                                    <td class="amount">
                                                                    @can('view', $row->user)
                                                                        <a class="font-weight-normal" href="{{route('users.show', $row->user)}}">{{$row->user->full_name}}</a>
                                                                    @else
                                                                        {{$row->user->full_name}}
                                                                    @endcan
                                                                    </td>
                                                                    <td class="status">
                                                                        <span class="badge badge-soft-{{$row->status->color}}">{{$row->status->label}}</span>
                                                                    </td>
                                                                    <td class="date">
                                                                        {{ Carbon::parse($row->created_at)->isoFormat("DD MMM YYYY") }}
                                                                    </td>
                                                                    <td>
                                                                        <ul class="list-inline hstack gap-2 mb-0">
                                                                            @can('view', $row)
                                                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Chat">
                                                                                    <a href="{{route('inquiries.show', $row)}}" class="text-info d-inline-block">
                                                                                        <i class="ri-message-3-line fs-16"></i>
                                                                                    </a>
                                                                                </li>
                                                                            @endcan
                                                                            @switch($row->status_code)
                                                                                @case("DEM_REJETEE")
                                                                                    @can('validate', App\Models\Inquiry::class)
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'accept']) }}" class="text-primary d-inline-block">
                                                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                                @break
                                                                                @case("DEM_ACCEPTEE")
                                                                                    @can('reject', App\Models\Inquiry::class)
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                                                <i class="ri-close-fill label-icon align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                                @break
                                                                                @default
                                                                                    @can('validate', App\Models\Inquiry::class)
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'accept']) }}" class="text-success d-inline-block">
                                                                                                <i class="ri-check-fill align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rejeter">
                                                                                            <a href="{{ route('inquiries.switch', ['inquiry' => $row, 'action' => 'reject']) }}" class="text-danger d-inline-block">
                                                                                                <i class="ri-close-fill align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                            @endswitch
                                                                            @can('validate', App\Models\Inquiry::class)
                                                                                @switch($row->marker)
                                                                                    @case(1)
                                                                                        @can('validate', App\Models\Inquiry::class)
                                                                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Archiver">
                                                                                                <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'archive']) }}" class="text-warning d-inline-block">
                                                                                                    <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                    @break
                                                                                    @case(2)
                                                                                        @can('validate', App\Models\Inquiry::class)
                                                                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Selectionner">
                                                                                                <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'shortList']) }}" class="text-primary d-inline-block">
                                                                                                    <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                    @break
                                                                                    @default
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Selectionner">
                                                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'shortList']) }}" class="text-primary d-inline-block">
                                                                                                <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Archiver">
                                                                                            <a href="{{ route('inquiries.marker', ['inquiry' => $row, 'action' => 'archive']) }}" class="text-warning d-inline-block">
                                                                                                <i class="ri-inbox-archive-line align-middle fs-16"></i>
                                                                                            </a>
                                                                                        </li>
                                                                                @endswitch
                                                                            @endcan
                                                                        </ul>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                              <tr>
                                                                <td colspan="4" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
                                                              </tr>
                                                            @endforelse

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="inquiry-archived" role="tabpanel">
                                                    <div class="table-responsive table-card mb-1 mt-3" style="margin-right:0px; margin-left: 0px;">
                                                        <table class="table table-nowrap align-middle" id="orderTable">
                                                            <thead class="text-muted table-light">
                                                                <tr class="text-uppercase">
                                                                    <th class="sort" data-sort="user">Client</th>
                                                                    <th class="sort" data-sort="status">Statut</th>
                                                                    <th class="sort" data-sort="date">Date</th>
                                                                    <th class="sort" data-sort="city"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="list form-check-all">
                                                            @forelse ($archivedInquiries->sortByDesc('created_at') as $row)
                                                                <tr>
                                                                    <td class="amount">
                                                                    @can('view', $row->user)
                                                                        <a class="font-weight-normal" href="{{route('users.show', $row->user)}}">{{$row->user->full_name}}</a>
                                                                    @else
                                                                        {{$row->user->full_name}}
                                                                    @endcan
                                                                    </td>
                                                                    <td class="status">
                                                                        <span class="badge badge-soft-{{$row->status->color}}">{{$row->status->label}}</span>
                                                                    </td>
                                                                    <td class="date">
                                                                        {{ Carbon::parse($row->created_at)->isoFormat("DD MMM YYYY") }}
                                                                    </td>
                                                                    <td>
                                                                        <ul class="list-inline hstack gap-2 mb-0">
                                                                        @can('view', $row)
                                                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Chat">
                                                                                <a href="{{route('inquiries.show', $row)}}" class="text-info d-inline-block">
                                                                                    <i class="ri-message-3-line fs-16"></i>
                                                                                </a>
                                                                            </li>
                                                                        @endcan
                                                                        </ul>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                              <tr>
                                                                <td colspan="4" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
                                                              </tr>
                                                            @endforelse

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end card body -->
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                        </div>
                        <div class="tab-pane fade" id="property-ratings" role="tabpanel">
                            <div class="row gx-lg-5">

                                <div class="col-xl-8">
                                    <div class="mt-xl-0 mt-5">
                                        <div>
                                            <div>
                                                <h5 class="fs-14 mb-3">Retours Client</h5>
                                            </div>
                                            <div class="row gy-4 gx-0">
                                                <div class="col-lg-4">
                                                    <div>
                                                        <div class="pb-3">
                                                            <div class="bg-light px-3 py-2 rounded-2 mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-grow-1">
                                                                        @if($avg_rating>=1 && $avg_rating<2)
                                                                        <div
                                                                            class="fs-16 align-middle text-warning">
                                                                            <i class="ri-star-fill"></i>
                                                                            <i class="ri-star-line"></i>
                                                                            <i class="ri-star-line"></i>
                                                                            <i class="ri-star-line"></i>
                                                                            <i class="ri-star-line"></i>
                                                                        </div>
                                                                            @elseif($avg_rating>=2 && $avg_rating<3)
                                                                            <div
                                                                                class="fs-16 align-middle text-warning">
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-line"></i>
                                                                                <i class="ri-star-line"></i>
                                                                                <i class="ri-star-line"></i>
                                                                            </div>

                                                                            @elseif($avg_rating>=3 && $avg_rating<4)
                                                                                <div
                                                                                    class="fs-16 align-middle text-warning">
                                                                                    <i class="ri-star-fill"></i>
                                                                                    <i class="ri-star-fill"></i>
                                                                                    <i class="ri-star-fill"></i>
                                                                                    <i class="ri-star-line"></i>
                                                                                    <i class="ri-star-line"></i>
                                                                                </div>

                                                                        @elseif($avg_rating>=4 && $avg_rating<5)
                                                                            <div
                                                                                class="fs-16 align-middle text-warning">
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-line"></i>
                                                                            </div>
                                                                        @elseif($avg_rating>=5 && $avg_rating<6)
                                                                            <div
                                                                                class="fs-16 align-middle text-warning">
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-fill"></i>
                                                                                <i class="ri-star-fill"></i>
                                                                            </div>
                                                                            @endif
                                                                    </div>
                                                                    <div class="flex-shrink-0">
                                                                        <h6 class="mb-0">{{ $avg_rating }} / 5</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-center">
                                                                <div class="text-muted">Total <span
                                                                        class="fw-medium">{{ number_format($total,0) }}</span> retours
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-3">
                                                            <div class="row align-items-center g-2">
                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0">5 star</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="p-2">
                                                                        <div
                                                                            class="progress animated-progress progress-sm">
                                                                            <div class="progress-bar bg-success"
                                                                                role="progressbar"
                                                                                style="width: {{ $prFive }}%"
                                                                                aria-valuenow="{{ $prFive }}"
                                                                                aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0 text-muted">{{ $fiveStar }}</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end row -->

                                                            <div class="row align-items-center g-2">
                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0">4 star</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="p-2">
                                                                        <div
                                                                            class="progress animated-progress progress-sm">
                                                                            <div class="progress-bar bg-success"
                                                                                role="progressbar"
                                                                                style="width: {{ $prFour }}%"
                                                                                aria-valuenow="{{ $prFour }}"
                                                                                aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0 text-muted">{{ $fourStar }}</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end row -->

                                                            <div class="row align-items-center g-2">
                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0">3 star</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="p-2">
                                                                        <div
                                                                            class="progress animated-progress progress-sm">
                                                                            <div class="progress-bar bg-success"
                                                                                role="progressbar"
                                                                                style="width: {{ $prThree }}%"
                                                                                aria-valuenow="{{ $prThree }}"
                                                                                aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0 text-muted">{{ $threeStar }}</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end row -->

                                                            <div class="row align-items-center g-2">
                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0">2 star</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="p-2">
                                                                        <div
                                                                            class="progress animated-progress progress-sm">
                                                                            <div class="progress-bar bg-warning"
                                                                                role="progressbar"
                                                                                style="width: {{ $prTwo }}%"
                                                                                aria-valuenow="{{ $prTwo }}"
                                                                                aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0 text-muted">{{ $twoStar }}</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end row -->

                                                            <div class="row align-items-center g-2">
                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0">1 star</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="p-2">
                                                                        <div
                                                                            class="progress animated-progress progress-sm">
                                                                            <div class="progress-bar bg-danger"
                                                                                role="progressbar"
                                                                                style="width: {{ $prOne }}%"
                                                                                aria-valuenow="{{ $prOne }}"
                                                                                aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="p-2">
                                                                        <h6 class="mb-0 text-muted">{{ $oneStar }}</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end row -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end col -->

                                                <div class="col-lg-8">
                                                    <div class="ps-lg-4">
                                                        <div class="d-flex flex-wrap align-items-start gap-3">
                                                            <h5 class="fs-14">Feedback: </h5>
                                                        </div>

                                                        <div class="me-lg-n3 pe-lg-4" data-simplebar
                                                            style="max-height: 225px;">
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach($reviews as $review)
                                                                <li class="py-2">
                                                                    <div
                                                                        class="border border-dashed rounded p-3">
                                                                        <div
                                                                            class="d-flex align-items-start mb-3">
                                                                            <div class="hstack gap-3">
                                                                                <div
                                                                                    class="badge rounded-pill bg-success mb-0">
                                                                                    <i class="mdi mdi-star"></i> {{ number_format($review->rating) }}
                                                                                </div>
                                                                                <div class="vr"></div>
                                                                                <div class="flex-grow-1">
                                                                                    <p class="text-muted mb-0">
                                                                                    {{ $review->review }}
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex align-items-end">
                                                                            <div class="flex-grow-1">
                                                                                <h5 class="fs-14 mb-0">{{ $review->user->name }}
                                                                                </h5>
                                                                            </div>

                                                                            <div class="flex-shrink-0">
                                                                                <p
                                                                                    class="text-muted fs-13 mb-0">
                                                                                   {{ $review->created_at }}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                @endforeach



                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end Ratings & Reviews -->
                                        </div>
                                        <!-- end card body -->
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
@section('script')
    <!-- glightbox js -->
    <script src="{{ URL::asset('assets/libs/glightbox/glightbox.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/swiper/swiper.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/property-show.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
