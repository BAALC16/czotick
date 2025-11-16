@extends('public.layouts')
@section('title') {{ $title }} @endsection
@section('content')

    <main id="content">
      <form class="mx-n1" id="accordion-5">
      <section class="pb-4 page-title shadow">
        <div class="container">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb pt-6 pt-lg-2 lh-15 pb-5">
              <li class="breadcrumb-item"><a href="/">Accueil</a></li>
              <li class="breadcrumb-item active" aria-current="page">Tous les Biens</li>
            </ol>
            <h1 class="fs-30 lh-1 mb-0 text-heading font-weight-600">
              {{ $title }}
            </h1>

            <div class="mt-6">
              <div class="form-inline mx-n1">
                <div class="form-group p-1">
                  <label for="ville" class="sr-only">Commune</label>
                  <select class="form-control border-0 shadow-xxs-1 bg-transparent font-weight-600 selectpicker" title="Commune" data-style="bg-white" id="location" name="ville">
                    <optgroup label="Grand Abidjan">
                    @foreach (App\Models\City::all() as $c)
                        <option value="{{ $c->id }}" {{ (request("ville", "") == $c->id ? "selected" : "") }}>{{ $c->name }}</option>
                    @endforeach
                    </optgroup>
                  </select>
                </div>
                @if ($type == "appartement" || $type == "villa")
                <div class="form-group p-1">
                  <label for="pieces" class="sr-only">Nombre de Pi&egrave;ces</label>
                  <select class="form-control border-0 shadow-xxs-1 bg-transparent font-weight-600 selectpicker" title="Nombre de Pi&egrave;ces" data-style="bg-white" id="location" name="pieces">
                  @foreach (App\Models\LayoutType::all() as $l)
                      <option value="{{ $l->id }}" {{ (request("pieces", "") == $l->id ? "selected" : "") }}>{{ $l->name }}</option>
                  @endforeach
                  </select>
                </div>
                @endif
                @if ($type == "villa")
                <div class="form-group p-1">
                  <label for="villatype" class="sr-only">Type de Villa</label>
                  <select class="form-control border-0 shadow-xxs-1 bg-transparent font-weight-600 selectpicker" title="Type de Villa" data-style="bg-white" id="location" name="villatype">
                  @foreach (App\Models\VillaType::all() as $v)
                      <option value="{{ $v->id }}"{{ (request("villatype", "") == $v->id ? "selected" : "") }}>{{ $v->name }}</option>
                  @endforeach
                  </select>
                </div>
                @endif
                <div class="form-group p-1">
                  <label for="prixmin" class="sr-only">@if($transaction == "location") Loyer @else Prix @endif min.</label>
                  <select class="form-control border-0 shadow-xxs-1 bg-transparent font-weight-600 selectpicker" title="@if($transaction == "location") Loyer @else Prix @endif min." data-style="bg-white" id="location" name="prixmin">
                  @php
                    $start = $transaction == "location" ? 50000 : 10000000;
                    $end = $transaction == "location" ? 1000000 : 1000000000;
                    $inc = $transaction == "location" ? 10000 : 10000000;
                  @endphp
                  @for ($price = $start; $price <= $end; $price += $inc)
                      <option value="{{ $price }}"{{ (request("prixmin", "") == $price ? "selected" : "") }}>>= @money($price) FCFA</option>
                    @php
                      if ($price >= $inc * 10) $inc *= 10;
                    @endphp
                  @endfor
                  </select>
                </div>
                <div class="form-group p-1">
                  <label for="prixmax" class="sr-only">@if($transaction == "location") Loyer @else Prix @endif max.</label>
                  <select class="form-control border-0 shadow-xxs-1 bg-transparent font-weight-600 selectpicker" title="@if($transaction == "location") Loyer @else Prix @endif max." data-style="bg-white" id="location" name="prixmax">
                  @php
                    $start = $transaction == "location" ? 50000 : 10000000;
                    $end = $transaction == "location" ? 1000000 : 1000000000;
                    $inc = $transaction == "location" ? 10000 : 10000000;
                  @endphp
                  @for ($price = $start; $price <= $end; $price += $inc)
                      <option value="{{ $price }}"{{ (request("prixmax", "") == $price ? "selected" : "") }}><= @money($price) FCFA</option>
                    @php
                      if ($price >= $inc * 10) $inc *= 10;
                    @endphp
                  @endfor
                  </select>
                </div>
                <div class="form-group p-1">
                  <label for="surfacemin" class="sr-only">Surface min.</label>
                  <select class="form-control border-0 shadow-xxs-1 bg-transparent font-weight-600 selectpicker" title="Surface min." data-style="bg-white" id="location" name="surfacemin">
                  @php
                    $inc = 100
                  @endphp
                  @for ($area = 100; $area <= 2000; $area += $inc)
                      <option value="{{ $area }}"{{ (request("surfacemin", "") == $area ? "selected" : "") }}>>= {{ $area }} m2</option>
                    @php
                      if ($area >= $inc * 10) $inc *= 10;
                    @endphp
                  @endfor
                  </select>
                </div>
                <button type="submit" class="btn btn-primary shadow-none ml-3">Rechercher ...</button>
            </div>
          </nav>
        </div>
      </section>
      <section class="pt-6 pb-7">
        <div class="container">
          <div class="row align-items-sm-center">
            <div class="col-md-6">
              <h2 class="fs-15 text-dark mb-0">Nous avons trouv&eacute; <span class="text-primary">{{ $properties->count() }}</span> @if ($properties->count() > 1) biens qui pourraient @else bien qui pourrait @endif vous int&eacute;resser
              </h2>
            </div>
            <div class="col-md-6 mt-6 mt-md-0">
              <form class="form-inline mx-n1" id="accordion-5">
              <div class="d-flex justify-content-md-end align-items-center">
                <div class="input-group border rounded input-group-lg w-auto bg-white mr-3">
                  <label class="input-group-text bg-transparent border-0 text-uppercase letter-spacing-093 pr-1 pl-3" for="inputGroupSelect01"><i class="fas fa-align-left fs-16 pr-2"></i>Trier Par:</label>
                  <select class="form-control border-0 bg-transparent shadow-none p-0 selectpicker sortby select-submit" data-style="bg-transparent border-0 font-weight-600 btn-lg pl-0 pr-3" id="inputGroupSelect01" name="filtre">
                    @php
                      $filters = array(
                        "date_desc" => "Date",
                        "prix_asc" => "Prix Croissant",
                        "prix_desc" => "Prix Décroissant",
                        "surface_asc" => "Surface Croissante",
                        "surface_desc" => "Surface Décroissante",
                      );
                    @endphp
                    @foreach ($filters as $f=>$v)
                        <option value="{{ $f }}"{{ (request("filtre", "") == $f ? "selected" : "") }}>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      </form>
      <section class="pb-9 pb-md-11">
        <div class="container">
        @foreach ($properties as $property)
          <div class="media p-4 border rounded-lg shadow-hover-1 pr-lg-8 mb-6 flex-column flex-lg-row no-gutters" data-animate="fadeInUp">
            <div class="col-lg-4 mr-lg-5 card border-0 hover-change-image bg-hover-overlay">
          @if (!($property->gallery->isEmpty()))
              @if (Storage::disk('public')->exists('property/gallery/'.$property->gallery->first()->name))
              <img src="{{Storage::url('property/gallery/'.$property->gallery->first()->name)}}" class="card-img" alt="{{$property->title}}">
              @endif
          @endif
              <div class="card-img-overlay p-2 d-flex flex-column">
                <div>
                  @if($property->propertyType->name == "Terrain")
                  <span class="badge badge-indigo">{{$property->propertyType->name}}</span>
                  @else
                  <span class="badge badge-orange">{{$property->propertyType->name}}</span>
                  @endif
                  @if ($property->purpose == "location")
                  <span class="badge mr-2 badge-primary">{{ $property->purpose }}</span>
                  @else
                  <span class="badge mr-2 badge-orange">{{ $property->purpose }}</span>
                  @endif
                </div>
                <div class="mt-auto d-flex hover-image">
                  <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                    <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                      <a href="#" class="text-white hover-primary">
                        <i class="far fa-images"></i><span class="pl-1">9</span>
                      </a>
                    </li>
                    <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                      <a href="#" class="text-white hover-primary">
                        <i class="far fa-play-circle"></i><span class="pl-1">2</span>
                      </a>
                    </li>
                  </ul>
                  <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                    <li class="list-inline-item mr-3 h-32" data-toggle="tooltip" title="Wishlist">
                      <a href="@auth(){{'#'}} @else {{route('login')}} @endauth"
                         data-id="{{ $property->id }}"
                         data-type="property"  class="text-white wishlist @if($property->has_wishlist) {{"wishlist-added"}} @endif fs-20 hover-primary">

                        <i class="@if($property->has_wishlist) {{'fas'}} @else {{'far'}} @endif fa-heart"></i>
                      </a>
                    </li>
                    <li class="list-inline-item mr-3 h-32" data-toggle="tooltip" title="Compare">
                      <a href="#" class="text-white fs-20 hover-primary">
                        <i class="fas fa-exchange-alt"></i>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="media-body mt-5 mt-lg-0">
              <h2 class="my-0">
                <a href="{{ route('public.property', ['property' => $property, 'address' => str_slug($property->fullAddress()), 'title' => $property->slug]) }}"
                              class="text-dark hover-primary">{{ $property->title }}</a>
              </h2>
              <p class="mb-2 font-weight-500 text-gray-light">{{ $property->fullAddress() }}</p>
              <p class="mb-6 mxw-571 ml-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut</p>
              <div class="d-lg-flex justify-content-lg-between">
                <ul class="list-inline d-flex mb-0 flex-wrap">

                    @if (!empty($property->bedroom))
                    <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5"
                        data-toggle="tooltip" title="3 Bedroom"><svg
                            class="icon icon-bedroom fs-18 text-primary mr-1">
                            <use xlink:href="#icon-bedroom"></use>
                        </svg>{{ $property->bedroom }}</li>
                    @endif
                    @if (!empty($property->bathroom))
                    <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5"
                        data-toggle="tooltip" title="3 Bathrooms"><svg
                            class="icon icon-shower fs-18 text-primary mr-1">
                            <use xlink:href="#icon-shower"></use>
                        </svg>{{ $property->bathroom }}</li>
                    @endif
                  <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5" data-toggle="tooltip" title="Size">
                    <svg class="icon icon-square fs-18 text-primary mr-1">
                      <use xlink:href="#icon-square"></use>
                    </svg>
                    {{ $property->area }} m2
                  </li>
                </ul>
                <p class="fs-22 font-weight-bold text-heading lh-1 mb-0 pr-lg-3 mb-lg-2 mt-3 mt-lg-0">
                  @money($property->price) FCFA
                </p>
              </div>
            </div>
          </div>
          @endforeach
          <nav class="pt-4">
            <ul class="pagination rounded-active justify-content-center mb-0">
              {{ $properties->links() }}
            </ul>
          </nav>
        </div>
      </section>
    </main>
@endsection
