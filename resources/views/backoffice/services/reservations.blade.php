@extends('backoffice.layouts')
@section('title')
    Demandes de Services
@endsection
{{-- @section('specific-css')
  <link rel="stylesheet" href="/vendors/dataTables/jquery.dataTables.min.css">
@endsection --}}
@section('content')
    <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10">
        <div class="d-flex flex-wrap flex-md-nowrap mb-6">
            <div class="mr-0 mr-md-auto">
                <h2 class="mb-0 text-heading fs-22 lh-15">Demandes de Services<span
                        class="badge badge-white badge-pill text-primary fs-18 font-weight-bold ml-2">{{$reservations->count()}}</span>
                </h2>
                <p>Rechercher des demandes de services</p>
            </div>
            <form class="form">
                <div class="input-group input-group-lg bg-white border">
                    <div class="input-group-prepend">
                        <button class="btn pr-0 shadow-none" type="button"><i class="far fa-search"></i></button>
                    </div>
                    <input type="text" class="form-control bg-transparent border-0 shadow-none text-body"
                        placeholder="Rechercher dans la liste" name="search">
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover bg-white border rounded-lg">
                <thead class="thead-sm thead-black">
                    <tr>
                        <th scope="col" class="border-top-0 px-6 pt-6 pb-3 text-uppercase">Service</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3 text-uppercase">Utilisateur</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3 text-uppercase">Assignée à</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3 text-uppercase">Statut</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3 text-uppercase">Date</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3">Actions</th>
                        <th scope="col" class="border-top-0 pt-6 pb-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations->sortByDesc('created_at') as $row)
                        <tr>
                          <td>
                              @can('view', $row->service)
                                <a class="font-weight-normal" href="{{route('services.show', $row->service)}}">{{$row->service->label}}</a>
                              @else
                                {{$row->service->label}}
                              @endcan
                          </td>
                          <td>
                              @can('view', $row->user)
                                <a class="font-weight-normal" href="{{route('users.show', $row->user)}}">{{$row->user->full_name}}</a>
                              @else
                                {{$row->user->full_name}}
                              @endcan
                          </td>
                          <td>
                              @if($row->prestataire)
                                  @can('view', $row->user)
                                      <a class="font-weight-normal" href="{{route('users.show', $row->prestataire)}}">{{$row->prestataire->full_name}}</a>
                                  @else
                                    {{$row->prestataire->full_name}}
                                  @endcan
                              @else
                                -
                              @endif
                          </td>
                          <td>
                            <span class="badge font-weight-normal fs-12 badge-{{$row->status->color}}">{{$row->status->label}}</span>
                          </td>
                          <td>
                            {{Carbon\Carbon::parse($row->created_at)->isoFormat('DD MMM YYYY, HH:mm')}}
                          </td>
                          <td class="align-middle"><a href="{{route('reservations.show', $row)}}"
                                  class="btn btn-sm bg-hover-light border fs-14 px-3">Voir
                                  <span class="d-inline-block ml-1 text-primary "><i class="fal fa-eye"></i></span></a>
                                  @if($row->comments_count > 0)
                                    <span data-toggle="tooltip" title="Messages" class="d-inline-block fs-15 text-muted hover-primary ml-1"><i class="far fa-comments"></i> {{$row->comments_count}}</span>
                                @endif
                          </td>
                          <td class="align-middle">
                              @can('update', $row)
                                <a href="{{ route('reservations.edit', $row) }}" class="d-inline-block fs-18 text-muted hover-primary mr-5"><i
                                    class="fal fa-pencil-alt"></i></a>
                              @endcan
                              @can('delete', $row)
                                <a href="javascript:;" data-confirm="Souhaitez-vous vraiment supprimer cette demande ?" data-href="{{ route('reservations.destroy', $row) }}" data-toggle="tooltip" title="Supprimer" class="click-to-delete-row d-inline-block fs-18 text-muted hover-primary"><i
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
          {{$reservations->withQueryString()->links()}}
        </div>
    </div>

@endsection
@section('specific-js')
    {{-- <script src="/vendors/dataTables/jquery.dataTables.min.js"></script> --}}
    <script type="text/javascript">
        $('#sort_box').change(function() {
            $('#search_filter_form')[0].submit();
        })
    </script>
@endsection
