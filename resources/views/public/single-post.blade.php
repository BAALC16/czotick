@extends('public.layouts_index')
@section('content')
<section class="page-title divider layer-overlay overlay-dark-5 section-typo-light bg-img-center" data-tm-bg-img="{{ URL::asset('assets/front/images/ban.png') }}">
    <div class="container pt-90 pb-90">
      <!-- Section Content -->
      <div class="section-content">
        <div class="row">
          <div class="col-md-12 text-center">
            <h2 class="title text-white">{{ $post->title }}</h2>
            {{ Breadcrumbs::render('public.single-post', $post) }} 
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Section: inner-header End -->

  <section>
    <div class="container mt-30 mb-30 pt-30 pb-30">
      <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="blog-posts single-post">
                <article class="post clearfix mb-0">
                    <div class="entry-header mb-30">
                        <div class="post-thumb thumb"> <img src="/public/{{ $post->image }}" alt="images" class="img-responsive img-fullwidth"> </div>
                        <h2>{{ $post->title }}</h2>
                        <div class="entry-meta mt-0">
                        <span class="mb-10 text-gray-darkgray mr-10 font-size-13"><i class="far fa-calendar-alt mr-10 text-theme-colored1"></i> {{ Carbon::parse( $post->created_at)->isoFormat("DD MMMM YYYY") }}</span>
                        {{-- <span class="mb-10 text-gray-darkgray mr-10 font-size-13"><i class="far fa-comments mr-10 text-theme-colored1"></i> 214 Comments</span> --}}
                        </div>
                    </div>
                    <div class="entry-content" style="text-align: justify;">
                        {!! $post->content !!}
                    </div>
                </article>
            </div>
        </div>
      </div>
    </div>
  </section>
@endsection

