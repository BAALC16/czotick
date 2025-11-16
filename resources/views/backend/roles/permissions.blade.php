@extends('backend.layouts')
@section('title')
  Rôles
@endsection

@section('content')
  <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10">
    <div class="mb-6">
      <h2 class="mb-0 text-heading fs-22 lh-15">Permissions</h2>
    </div>
    <div class="card card-flush">
      <!--begin::Card body-->
      <div class="card-body pt-0">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="kt_permissions_table">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="text-start text-gray-400 font-weight-bold text-uppercase gs-0">
              <th class="min-w-250px">Description</th>
              <th class="min-w-250px">Assignée à</th>
              <th class="text-end min-w-100px">Actions</th>
            </tr>
            <!--end::Table row-->
          </thead>
          <!--end::Table head-->
          <!--begin::Table body-->
          <tbody>
            @foreach ($permissions->sortBy('description') as $permission)
              <tr>
                <!--begin::Name=-->
                <td class="font-weight-bold">{{$permission->description}}</td>
                <!--end::Name=-->
                <!--begin::Assigned to=-->
                <td>
                  @forelse ($permission->roles->sortBy('nom') as $k => $role)
                    @can('view', $role)
                      <a href="{{route('roles.show', $role)}}" class="badge badge-{{[
                        'primary',
                        'info',
                        'danger',
                        'success',
                        'warning',
                      ][$k%5]}} m-1">{{$role->nom}}</a>
                    @else
                      <span class="badge badge-{{[
                        'primary',
                        'info',
                        'danger',
                        'success',
                        'warning',
                      ][$k%5]}} m-1">{{$role->nom}}</span>
                    @endcan
                  @empty
                    <em>Aucun Rôle</em>
                  @endforelse
                </td>
                <!--end::Assigned to=-->
                <!--begin::Action=-->
                <td class="text-right">
                  @can('update', $permission)
                    <button class="btn text-center btn-icon btn-active-light-primary w-30px h-30px" data-toggle="modal" data-target="#modal_update_permission" data-permission='@json($permission)' onclick="$(this).populateUpdateForm();">
                      <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="black" />
                          <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="black" />
                        </svg>
                      </span>
                    </button>
                    <!--end::Update-->                    
                  @endcan
                </td>
                <!--end::Action=-->
              </tr>
            @endforeach
          </tbody>
          <!--end::Table body-->
        </table>
        <!--end::Table-->
      </div>
      <!--end::Card body-->
    </div>


    <div class="modal fade" id="modal_update_permission" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel2" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <span class="h6 modal-title" id="staticBackdropLabel2">Modifier la Permission</span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

            <div class="alert alert-primary d-flex border-primary border border-dashed" role="alert">
              <span class="svg-icon svg-icon-2tx svg-icon-primary h2 text-primary mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                  <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" />
                  <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                </svg>
              </span>
              <div class="d-flex flex-stack flex-grow-1">
                <!--begin::Content-->
                <div>
                  <div class="text-gray-700">
                    <strong class="mr-1">Remarque : </strong><span class="font-weight-light">La gestion des Permissions est interne à la logique du code source. Les modifications faites ici ne changeront pas le fonctionnement des autorisations pour les Rôles auxquels cette Permission est assignée.</span>
                  </div>
                </div>
                <!--end::Content-->
              </div>
            </div>
            <form id="modal_update_permission_form" class="form form-xhr" action="#" method="POST" data-action="{{route('permissions.update', '__id__')}}">
              @csrf
              <div class="form-group mt-8">
                <label class="form-label">
                  Description
                  <i class="fal fa-exclamation-circle ml-2" data-toggle="popover" data-trigger="hover" data-html="true" data-content="Les descriptions des Rôles sont censées êtres uniques."></i>
                </label>
                <input class="form-control form-control-lg border-0" placeholder="Entrez la description" name="description" />
              </div>
              <div class="text-center pt-3">
                <button type="reset" class="btn btn-light mr-3" data-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('specific-js')
  <script>
    jQuery(function(){
      $.fn.populateUpdateForm = function() {
        const permission = $(this).data('permission')
        $('#modal_update_permission_form').attr('action', $('#modal_update_permission_form').data('action').replace(/__id__/g, permission.id))
        $('#modal_update_permission_form [name=description]').val(permission.description)
      }
    })
  </script>
@endsection
