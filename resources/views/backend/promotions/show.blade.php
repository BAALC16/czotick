@extends('layouts.master')
@section('title') D&eacute;tails de la Promotion @endsection
@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/swiper/swiper.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/libs/glightbox/glightbox.min.css') }}" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Promotions @endslot
        @slot('title') D&eacute;tails de la Promotion @endslot
    @endcomponent
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row gx-lg-5">
                        <div class="col-xl-4 col-md-8 mx-auto">
                            <div class="product-img-slider sticky-side-div">
                                <div class="swiper product-thumbnail-slider p-2 rounded bg-light">
                                    <div class="swiper-wrapper">
                                    @if (!($promotion->images->isEmpty()))
                                        @foreach($promotion->images as $image)
                                        @if (Storage::disk('public')->exists('promotion/gallery/'.$image->name))
                                        <div class="swiper-slide">
                                            <a class="image-popup" href="{{Storage::url('promotion/gallery/'.$image->name)}}">
                                            <img src="{{Storage::url('promotion/gallery/'.$image->name)}}" alt="{{$promotion->title}}" class="img-fluid d-block"/>
                                        </a>
                                        </div>
                                        @endif
                                        @endforeach
                                    @endif
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                                <!-- end swiper thumbnail slide -->
                                <div class="swiper product-nav-slider mt-2">
                                    <div class="swiper-wrapper">
                                    @if (!($promotion->images->isEmpty()))
                                        @foreach($promotion->images as $image)
                                        @if (Storage::disk('public')->exists('promotion/gallery/'.$image->name))
                                        <div class="swiper-slide">
                                            <img src="{{Storage::url('promotion/gallery/'.$image->name)}}" alt="{{$promotion->title}}"
                                                class="img-fluid d-block" />
                                        </div>
                                        @endif
                                        @endforeach
                                    @endif
                                    @if (!empty($promotion->floor_plan) && Storage::disk('public')->exists('promotion/floor/'.$promotion->floor_plan))
                                        <div class="swiper-slide">
                                            <img src="{{Storage::url('promotion/floor/'.$promotion->floor_plan)}}" alt="{{$promotion->title}}"
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
                                        <h4>{{ $promotion->title }}@if ($promotion->layoutType) - {{ $promotion->layoutType->name }} @endif</h4>
                                        <div class="hstack gap-3 flex-wrap icon-demo-content">
                                            <div><a href="#" class="text-primary d-block">{{ $promotion->fullAddress() }}</a></div>
                                            <div class="vr"></div>
                                            <div class="text-muted">Publi&eacute; le : <span class="text-body fw-medium">{{ $promotion->dateCreated() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div>
                                        @can('validate', App\Models\Promotion::class)
                                        @if ($promotion->published)
                                            <a href="{{ route('promotion.switch', ['promotion' => $promotion, 'action' => 'reject']) }}" class="btn btn-danger"><i class="las la-pause"></i></a>
                                        @else
                                            <a href="{{ route('promotion.switch', ['promotion' => $promotion, 'action' => 'accept']) }}" class="btn btn-success"><i class="las la-play"></i></a>
                                        @endif
                                        @endcan
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-muted">
                                    <h5 class="fs-14">Description :</h5>
                                    <p>{!! $promotion->description !!}</p>
                                </div>
                                <div>

                                    <div class="mt-3">
                                        <h5 class="fs-14 mb-3">Requ&ecirc;tes</h5>
                                    </div>
                                    <div class="table-responsive table-card mb-1 mt-3" style="margin-right:0px; margin-left: 0px;">
                                        <table class="table table-nowrap align-middle" id="orderTable">
                                            <thead class="text-muted table-light">
                                                <tr class="text-uppercase">
                                                    <th class="sort" data-sort="user">Nom</th>
                                                    <th class="sort" data-sort="status">Email</th>
                                                    <th class="sort" data-sort="date">Phone</th>
                                                    <th class="sort" data-sort="date">Date</th>
                                                    <th class="sort" data-sort="city">Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                            @forelse ($promotion->inquiries->sortByDesc('created_at') as $row)
                                                <tr>
                                                    <td class="amount">
                                                        {{$row->name}}
                                                    </td>
                                                    <td class="amount">
                                                        {{$row->email}}
                                                    </td>
                                                    <td class="amount">
                                                        {{$row->phone}}
                                                    </td>
                                                    <td class="date">
                                                        {{ Carbon::parse($row->created_at)->isoFormat("DD MMM YYYY") }}
                                                    </td>
                                                    <td class="amount">
                                                        {{$row->notes}}
                                                    </td>
                                                </tr>
                                            @empty
                                              <tr>
                                                <td colspan="4" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
                                              </tr>
                                            @endforelse

                                            </tbody>
                                        </table>
                                        <div class="noresult" style="display: none">
                                            <div class="text-center">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px">
                                                </lord-icon>
                                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                                <p class="text-muted">We've searched more than 150+ Orders We did
                                                    not find any
                                                    orders for you search.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                        </div>
                        <!-- end col -->
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
    <script src="{{ URL::asset('assets/js/pages/promotion-show.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
