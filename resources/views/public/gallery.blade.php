@extends('public.layouts_index')
@section('content')
<section class="page-title divider layer-overlay overlay-dark-5 section-typo-light bg-img-center" data-tm-bg-img="{{ URL::asset('assets/front/images/ban.png') }}">
    <div class="container pt-90 pb-90">
      <!-- Section Content -->
      <div class="section-content">
        <div class="row">
          <div class="col-md-12 text-center">
            <h2 class="title text-white">Galérie</h2>
            {{ Breadcrumbs::render('public.gallery') }} 
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Section: inner-header End -->

  <section>
    <div class="container pb-70">
      <div class="section-content">
        <div class="row">
          <div class="col-sm-12">
            <div class="tm-sc-gallery tm-sc-gallery-grid gallery-style1-basic">
              <!-- Isotope Filter -->
              <div class="isotope-layout-filter filter-style-3 text-center cat-filter-theme-colored1" data-link-with="gallery-holder-743344">
                <a href="#" class="active" data-filter="*" style="text-transform: none;">Toutes</a>
                @foreach($categories as $category)
                    <a style="text-transform: none;" href="#{{ $category->slug }}" data-filter=".{{ $category->slug }}">{{ $category->label }}</a>

                @endforeach
            
              </div>
              <!-- End Isotope Filter -->
              <!-- Isotope Gallery Grid -->
              <div id="gallery-holder-743344" class="isotope-layout grid-3 gutter-10 clearfix lightgallery-lightbox">
                <div class="isotope-layout-inner">
                    @forelse($galleries as $gallery)
                        <!-- Isotope Item Start -->
                        <div class="isotope-item {{ $gallery->category->slug }}">
                            <div class="isotope-item-inner">
                            <div class="tm-gallery">
                                <div class="tm-gallery-inner">
                                <div class="thumb">
                                    <a href="#"><img src="{{ url('public/storage/'.$gallery->image) }}" class="" alt="images"/></a>
                                </div>
                                <div class="tm-gallery-content-wrapper">
                                    <div class="tm-gallery-content">
                                    <div class="tm-gallery-content-inner">
                                        <div class="icons-holder-inner">
                                        <div class="styled-icons icon-dark icon-circled icon-theme-colored1">
                                            <a class="lightgallery-trigger styled-icons-item" data-exthumbimage="{{ url('public/storage/'.$gallery->image) }}" title="photo" href="{{ url('public/storage/'.$gallery->image) }}"><i class="fa fa-plus"></i></a>
                                        </div>
                                        </div>
                                        {{-- <div class="title-holder">
                                        <h5 class="title"><a href="#">Demo Gallery 1</a></h5>
                                        </div> --}}
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <!-- Isotope Item End -->
                    @empty

                    @endforelse
                  <!-- the loop -->
                 
                  <!-- end of the loop -->
                </div>
              </div>
              <!-- End Isotope Gallery Grid -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
        
@endsection
