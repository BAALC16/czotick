@extends('layouts.master')
@section('title') @lang('translation.orders') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Roles @endslot
@slot('title') Tous les Roles @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des R&ocirc;les</h5>
                    <div class="flex-shrink-0">
                          @can('create', App\Models\Role::class)
                            <a href="{{ route('services.create') }}" class="btn btn-success add-btn" id="create-btn" >Ajouter </a>
                          @endcan
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">    
                  @foreach ($roles as $r)
                    <div class="col-sm-6">
                      <div class="card">
                        <div class="card-header">
                          <h6 class="card-title mb-0"><i class="ri-settings-4-fill align-middle me-1 lh-1"></i> {{ $r->nom }}</h6>
                        </div>
                        <div class="card-body">
                          @can('viewAny', App\Models\Role::class)
                            <div class="text-small text-muted my-0 ">
                              Utilisateurs ayant ce Rôle : {{$r->users_count}}
                            </div>                
                          @endcan
                          <!--begin::Permissions-->
                          <div class="d-flex flex-column">
                            @forelse ($r->permissions as $key => $perm)
                              @break($key >= 5)
                              <div class="d-flex align-items-center py-0">
                                <span class="mr-3">&mdash;</span>{{$perm->description}}
                              </div>
                            @empty
                              <div class="d-flex align-items-center py-2">
                                <span class="mr-3 text-italic">&mdash;</span>Aucune permission
                              </div>
                            @endforelse
                            @if($r->permissions->count() > 5)
                              <div class='d-flex align-items-center py-2'>
                                <span class='mr-3'>&mdash;</span>
                                <em>Et {{($diff = ($r->permissions->count() - 5)) . ' '.($diff > 1 ? 'autres' : 'autre')}} ...</em>
                              </div>
                            @endif
                          </div>
                          <!--end::Permissions-->
                        </div>
                        <!--begin::Card footer-->
                        <div class="card-footer">
                            <div class="hstack gap-2 justify-content-end">
                              @can('update', $r)
                                <a class="btn btn-link btn-sm link-success" data-toggle="modal" data-target="#modal_update_role" onclick=""><i class="ri-edit-line align-middle lh-1"></i>Modifier</a>
                              @endcan
                              @can('view', $r)
                                <a href="{{route('roles.show', $r)}}" class="btn btn-primary btn-sm">Voir le Rôle</a>
                              @endcan
                            </div>
                        </div>
                        <!--end::Card footer-->
                      </div>
                    </div>
                  @endforeach             
            </div>
        </div>

    </div>
    <!--end col-->
</div>
<!--end row-->
@endsection
@section('script')
<script src="assets/libs/list.js/list.js.min.js"></script>
        <script src="assets/libs/list.pagination.js/list.pagination.js.min.js"></script>

        <!--ecommerce-customer init js -->
        <script src="assets/js/pages/ecommerce-order.init.js"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
