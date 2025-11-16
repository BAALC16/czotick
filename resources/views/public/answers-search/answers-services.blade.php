@extends("public.layouts")
@section('title') Services @endsection
@section('content')
  <section class="pt-2 pb-10 pb-lg-17 page-title bg-overlay bg-img-cover-center"
     style="background-image: url('/images/BG3.jpg');">
        <div class="container">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-light mb-0">
              <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
              <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
          </nav>
        </div>
      </section>
      <section class="bg-patten-05 mb-13">
        <div class="container">
          <div class="card mt-n13 z-index-3 pt-10 border-0">
            <div class="card-body p-0">
              <h2 class="text-dark lh-1625 text-center mb-2">Services Offerts</h2>
              <p class="mxw-751 text-center mb-8 px-8">D&eacute;couvrez notre liste de services. Cliquez sur chaque service pour en savoir un peu plus.</p>
            </div>
          </div>
          <div class="row mb-9">
            @forelse ($suggestions as $service)

            <div class="col-sm-6 col-lg-4 mb-6">
                <div class="box px-0 py-6">
                  <div class="card border-hover shadow-hover-lg-1 pb-6 pt-4 h-100 bg-transparent border-0 py-7 px-4 bg-hover-white">
                      <div class="d-flex justify-content-center card-img-top">
                        <a href="{{ route('public.services.show', ['service' => $service, 'slug' => $service->slug]) }}">
                            @if (!empty($service->image))
                            <img src="{{ $service->image}}" alt="{{ $service->label }}">
                            @endif
                        </a>
                      </div>
                      <div class="card-body px-0 pt-5 pb-0 text-center">
                          <h4 class="card-title fs-16 lh-13 text-dark mb-2 px-10"><a class="text-dark" href="{{ route('public.services.show', ['service' => $service, 'slug' => $service->slug]) }}">{{ $service->label }}</a></h4>
                          <p class="card-text text-body px-lg-8">{{ Str::limit(strip_tags($service->description), 120) }}</p>
                      </div>
                      <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
                          <p class="fs-17 font-weight-bold text-heading mb-0"></p>
                          <ul class="list-inline mb-0">
                              <li class="list-inline-item">
                                  <a href="@auth(){{'#'}} @else {{route('login')}} @endauth"
                                     data-id="{{ $service->id }}"
                                     data-type="service"
                                     class="w-40px h-40 border @if($service->has_wishlist) {{"wishlist-added"}} @endif rounded-circle wishlist d-inline-flex align-items-center justify-content-center text-body hover-secondary  border-hover-accent"
                                     data-toggle="tooltip" title="Wishlist"><i class="@if($service->has_wishlist){{ 'fas' }} @else {{'far'}} @endif fa-heart"></i></a>
                              </li>
                          </ul></div>
                  </div>
                </div>
              </div>
            @empty
              <div class="alert alert-primary" role="alert">
                Il n'y a rien à afficher pour le moment.
              </div>
            @endforelse
          </div>
        </div>
      </section>
@endsection
