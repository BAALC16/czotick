@extends('backoffice.layouts')
@section('title')
  Services
@endsection
{{-- @section('specific-css')
  <link rel="stylesheet" href="/vendors/dataTables/jquery.dataTables.min.css">
@endsection --}}
@section('content')
  <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10">
    <div class="d-flex flex-wrap flex-md-nowrap mb-6">
      <div class="mr-0 mr-md-auto">
        <h2 class="mb-0 text-heading fs-22 lh-15">Services<span
          class="badge badge-white badge-pill text-primary fs-18 font-weight-bold ml-2">{{ $services->count() }}</span>
        </h2>
        @can('create', App\Models\Service::class)
          <p>Pour ajouter de nouveaux services, <a href="{{ route('services.create') }}">cliquez ici</a>.</p>
        @endcan
      </div>
      <form method="get" id="search_filter_form">
        <div class="form-inline justify-content-md-end mx-n2">
          <div class="p-2">
            <div class="input-group input-group-lg bg-white border">
              <div class="input-group-prepend">
                <button class="btn pr-0 shadow-none" type="submit"><i class="far fa-search"></i></button>
              </div>
              <input type="text" class="form-control bg-transparent border-0 shadow-none text-body"
              placeholder="Rechercher" id="search_box" name="q" value="{{ request('q') }}">
            </div>
          </div>
          <div class="p-2">
            <div class="input-group input-group-lg bg-white border">
              <div class="input-group-prepend">
                <span class="input-group-text bg-transparent letter-spacing-093 border-0 pr-0"><i class="far fa-align-left mr-2"></i>Trier par :</span>
              </div>
              <select class="form-control bg-transparent pl-0 selectpicker d-flex align-items-center sortby" name="sort" data-style="bg-transparent px-1 py-0 lh-1 font-weight-600 text-body" id="sort_box">
                @foreach ([
                  "{'col':'label', 'order':'ASC'}" => "Alphabétique - A à Z",
                  "{'col':'label', 'order':'DESC'}" => "Alphabétique - Z à A",
                  "{'col':'prix', 'order':'DESC'}" => "Prix - Croissant",
                  "{'col':'prix', 'order':'ASC'}" => "Prix - Décroissant",
                  "{'col':'created_at', 'order':'ASC'}" => "Date - Plus anciens",
                  "{'col':'created_at', 'order':'DESC'}" => "Date - Plus récents",
                  ] as $key => $value)

                  <option value="{{ $key }}" @if(request('sort') == $key) selected @endif>{{ $value }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="table-responsive">
      <table class="services_datatable table table-hover bg-white border rounded-lg">
        <thead class="thead-sm thead-black">
          <tr>
            <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Libellé</th>
            <th scope="col" class="border-top-0 pt-5 pb-4">Prix (FCFA)</th>
            <th scope="col" class="border-top-0 pt-5 pb-4">Date d'ajout</th>
            <th scope="col" class="border-top-0 pt-5 pb-4">Statut</th>
            <th scope="col" class="border-top-0 pt-5 pb-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($services as $service)
            <tr class="shadow-hover-xs-2 bg-hover-white">
              <td class="align-middle pt-6 pb-4 px-6">
                <div class="media">
                  <div class="w-100px mr-4 position-relative">
                    @can('view', $service)
                      <a href="{{ route('services.show', $service) }}">
                        <img src="{{ $service->image_url }}" alt="{{ $service->label }}">
                      </a>
                    @else
                    <img src="{{ $service->image_url }}" alt="{{ $service->label }}">
                    @endcan
                  </div>
                  <div class="media-body">
                    @can('view', $service)
                      <a href="{{ route('services.show', $service) }}" class="text-dark hover-primary">
                        <h5 class="fs-16 mb-0 lh-18">{{ $service->label }}</h5>
                      </a>
                    @else
                      <h5 class="fs-16 mb-0 lh-18">{{ $service->label }}</h5>
                    @endcan
                    <span class="text-gray-light">Ajouté par <strong>{{ $service->owner ? $service->owner->prenoms : 'Système' }}</strong></span>
                  </div>
                </div>
              </td>
              <td class="align-middle">{{ $service->prix ?? "Prix non spécifié" }}</td>
              <td class="align-middle">{{ Carbon::parse($service->created_at)->isoFormat("DD MMM YYYY") }}</td>
              <td class="align-middle">
                @if($service->actif)
                  <span class="badge text-capitalize font-weight-normal fs-12 badge-green">Actif</span>
                @else
                  <span class="badge text-capitalize font-weight-normal fs-12 badge-yellow">Inactif</span>
                @endif
              </td>
              <td class="align-middle">
                @can('update', $service)
                  <a href="{{ route('services.edit', ['service' => $service, 'continue' => url()->full()]) }}" data-toggle="tooltip" title="Modifier" class="d-inline-block fs-18 text-muted hover-primary mr-5"><i class="fal fa-pencil-alt"></i></a>
                @endcan
                @can('delete', $service)
                  <a href="javascript:;" data-confirm="Souhaitez-vous vraiment supprimer ce service ?" data-href="{{ route('services.destroy', $service) }}" data-toggle="tooltip" title="Supprimer" class="click-to-delete-row d-inline-block fs-18 text-muted hover-primary"><i class="fal fa-trash-alt"></i></a>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $services->withQueryString()->links() }}
  </div>
@endsection
@section('specific-js')
  {{-- <script src="/vendors/dataTables/jquery.dataTables.min.js"></script> --}}
  <script type="text/javascript">
    $('#sort_box').change(function(){
      $('#search_filter_form')[0].submit();
    })
  </script>
@endsection
