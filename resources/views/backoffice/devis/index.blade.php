@extends('backoffice.layouts')
@section('title')
    Devis
@endsection
{{-- @section('specific-css')
  <link rel="stylesheet" href="/vendors/dataTables/jquery.dataTables.min.css">
@endsection --}}
@section('content')
    <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10">
        <div class="d-flex flex-wrap flex-md-nowrap mb-6">
            <div class="mr-0 mr-md-auto">
                <h2 class="mb-0 text-heading fs-22 lh-15">Devis<span
                        class="badge badge-white badge-pill text-primary fs-18 font-weight-bold ml-2">{{$devis->count()}}</span>
                </h2>
                <p>Rechercher des devis</p>
            </div>
            {{-- <form class="form">
                <div class="input-group input-group-lg bg-white border">
                    <div class="input-group-prepend">
                        <button class="btn pr-0 shadow-none" type="button"><i class="far fa-search"></i></button>
                    </div>
                    <input type="text" class="form-control bg-transparent border-0 shadow-none text-body"
                        placeholder="Rechercher dans la liste" name="search">
                </div>
            </form> --}}
        </div>
        <div class="table-responsive">
            <table class="table table-hover bg-white border rounded-lg">
                <thead class="thead-sm thead-black">
                    <tr>
                        <th scope="col" class="border-top-0 px-6 pt-6 pb-3 text-uppercase">Service</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3 text-uppercase">Utilisateur</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3 text-uppercase">Prestataire</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3 text-uppercase">Coût</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3 text-uppercase">Début exéc.</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3 text-uppercase">Fin exéc.</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($devis->sortByDesc('created_at') as $row)
                        <tr>
                          <td>
                              @can('view', $row->reservation->service)
                                <a class="font-weight-normal" href="{{route('services.show', $row->reservation->service)}}">{{$row->reservation->service->label}}</a>
                              @else
                                {{$row->reservation->service->label}}
                              @endcan
                          </td>
                          <td>
                              @can('view', $row->reservation->user)
                                <a class="font-weight-normal" href="{{route('users.show', $row->reservation->user)}}">{{$row->reservation->user->full_name}}</a>
                              @else
                                {{$row->reservation->user->full_name}}
                              @endcan
                          </td>
                          <td>
                              @can('view', $row->user)
                                  <a class="font-weight-normal" href="{{route('users.show', $row->user)}}">{{$row->user->full_name}}</a>
                              @else
                                {{$row->user->full_name}}
                              @endcan
                          </td>
                          <td>{{$row->cout}}</td>
                          <td>
                            {{Carbon\Carbon::parse($row->debut_execution)->isoFormat('DD MMM YYYY')}}
                          </td>
                          <td>
                            {{$row->fin_execution ? Carbon\Carbon::parse($row->debut_execution)->isoFormat('DD MMM YYYY') : '-'}}
                          </td>
                          <td class="align-middle">
                            <a href="{{route('reservations.show', $row->reservation)}}#devis"
                                  class="btn btn-sm bg-hover-light border fs-14 px-3">Voir
                                  <span class="d-inline-block ml-1 text-primary "><i class="fal fa-eye"></i></span></a>
                          </td>
                          <td class="align-middle">
                              @can('update', $row)
                                <a href="{{ route('reservations.devis.create', $row->reservation) }}" class="d-inline-block fs-18 text-muted hover-primary mr-5"><i
                                    class="fal fa-pencil-alt"></i></a>
                              @endcan
                              @can('delete', $row)
                                <a href="javascript:;" data-confirm="Souhaitez-vous vraiment supprimer ce devis ?" data-href="{{ route('devis.destroy', $row) }}" data-toggle="tooltip" title="Supprimer" class="click-to-delete-row d-inline-block fs-18 text-muted hover-primary"><i
                                        class="fal fa-trash-alt"></i></a>
                              @endcan
                          </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="6">Il n'y a rien à afficher pour le moment.</td>
                      </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
          {{$devis->withQueryString()->links()}}
        </div>
    </div>

@endsection
