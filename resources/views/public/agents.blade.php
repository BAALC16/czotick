@extends("public.layouts")
@section('title') Agents @endsection
@section('content')
    <main id="content">

      <form class="mx-n1" id="accordion-5">
      <section class="pb-4 page-title shadow">
        <div class="container">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb pt-6 pt-lg-2 lh-15 pb-5">
              <li class="breadcrumb-item"><a href="/">Accueil</a></li>
              <li class="breadcrumb-item active" aria-current="page">Tous les Agents</li>
            </ol>
            <h1 class="fs-30 lh-1 mb-0 text-heading font-weight-600">
              Tous les Agents
            </h1>

          </nav>
        </div>
      </section>
          <section class="pt-6 pb-7 bg-gray-01">
            <div class="container">
              <div class="row align-items-sm-center">
                <div class="col-md-6">
                  <h2 class="fs-15 text-dark mb-0">Nous avons <span class="text-primary">{{ $users->total() }}</span> @if ($users->total() > 1) agents @else agent @endif sur notre plateforme pour vous aider
                  </h2>
                </div>
              </div>
            </div>
          </section>
          <section class="pt-7 pb-13 bg-gray-01">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mb-6 mb-lg-0">
                        @foreach($users as $user)
                        @php
                            $company = $user->companies()->first();
                        @endphp
                          <div class="card p-2 border-0 mb-4 d-block">
                            <div class="row no-gutters">
                              <div class="col-sm-4 pr-0 pr-sm-1">
                                    <a href="{{ route('public.agent.show', $user->id) }}" class="d-block hover-shine">
                                        <img src="{{ $company->logo_url ?? $user->photo_url }}" class="card-img" alt="{{ $user->nom }}">
                                    </a>
                              </div>
                              <div class="col-sm-8">
                                <div class="card-body pl-0 pl-sm-7">
                                  <a href="{{ route('public.agent.show',$user->id) }}" class="card-title d-block fs-16 lh-2 text-dark font-weight-500 hover-primary mb-0">
                                    {{ $company->title }}
                                  </a>
                                  <p class="mb-3 card-text">
                                    {{ $company->description }} @if (!empty($company->web)) <small><a href="{{ $company->web}}" target="_blank">Visiter le site</a></small>@endif
                                  </p>
                                  <a href="tel:{{ $company->phone }}" class="d-block">
                                    <span class="text-primary"><i class="fal fa-phone"></i></span>
                                    <span class="d-inline-block ml-2 text-body">{{ $company->phone }}</span>
                                  </a>
                                  @if (!empty($company->physical_address))
                                  <div class="">
                                    <span class="text-primary"><i class="fal fa-map-marker-alt"></i></span>
                                    <span class="d-inline-block ml-2">{{ $company->physical_address }}</span>
                                  </div>
                                  @endif
                                  @if (!empty($company->address))
                                  <div class="">
                                    <span class="text-primary"><i class="fal fa-mailbox"></i></span>
                                    <span class="d-inline-block ml-2">{{ $company->address }}</span>
                                  </div>
                                  @endif
                                  <ul class="list-inline text-gray-lighter mt-4 mb-0">
                                    @if (!empty($company->twitter))
                                    <li class="list-inline-item m-0">
                                        <a href="{{ $company->twitter }}"
                                           class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                class="fab fa-twitter"></i></a>
                                    </li>
                                    @endif
                                    @if (!empty($company->facebook))
                                    <li class="list-inline-item mr-0 ml-2">
                                        <a  href="{{ $company->facebook }}"
                                           class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                class="fab fa-facebook-f"></i></a>
                                    </li>
                                    @endif
                                    @if (!empty($company->instagram))              
                                    <li class="list-inline-item mr-0 ml-2">
                                        <a  href="{{ $company->instagram }}"
                                           class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                class="fab fa-instagram"></i></a>
                                    </li>
                                    @endif
                                    @if (!empty($company->linkedin))   
                                    <li class="list-inline-item mr-0 ml-2">
                                        <a  href="{{ $company->linkedin }}"
                                           class="w-32px h-32 rounded bg-hover-primary bg-white hover-white text-body d-flex align-items-center justify-content-center border border-hover-primary"><i
                                                class="fab fa-linkedin-in"></i></a>
                                    </li>
                                    @endif
                                    
                                  </ul>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endforeach

                        {!! $users->links() !!}
                    </div>
                    <div class="col-lg-4 primary-sidebar sidebar-sticky" id="sidebar">
                        <div class="primary-sidebar-inner">
                            <div class="card mb-4">
                                <div class="card-body text-center pt-7 pb-6 px-0">
                                    <img src="images/contact-widget.jpg"
                                         alt="Want to become an Estate Agent ?">
                                    <div class="text-dark mb-6 mt-n2 font-weight-500">Vous voulez devenir
                                        <p class="mb-0 fs-18">Prestataire?</p>
                                    </div>
                                    <a href="{{ route('login') }}" class="btn btn-primary">Enregistrez-vous</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
