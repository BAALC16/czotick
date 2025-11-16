@extends('backend.layouts')
@section('title')
  {{ $role->nom }}
@endsection

@section('content')
  <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10">
    <div class="row">
      <!--begin::Sidebar-->
      <div class="col-md-4 mb-10">
        <!--begin::Card-->
        <div class="card card-flush">
          <!--begin::Card header-->
          <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title">
              <h5>{{$role->nom}}</h5>
            </div>
            <!--end::Card title-->
          </div>
          <!--end::Card header-->
          <!--begin::Card body-->
          <div class="card-body pt-3">
            @if($role->description)
              <div class="text-muted mb-3">
                <small>{{ $role->description }}</small>
              </div>
            @endif
            <!--begin::Permissions-->
            <ul>
              @foreach ($role->permissions as $perm)
                <li>{{$perm->description}}</li>
              @endforeach
            </ul>
            <!--end::Permissions-->
          </div>
          <!--end::Card body-->
          @can('update', $role)
            <!--begin::Card footer-->
            <div class="card-footer pt-3">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_update_role">Modifier le Rôle</button>
            </div>
            <!--end::Card footer-->
          @endcan
        </div>
        <!--end::Card-->

        @can('update', $role)
          <div class="modal fade" id="modal_update_role" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="staticBackdropLabel2">Modifier le Rôle</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="form_update_role" class="form-xhr" method="POST" data-action="{{route('roles.update', $role)}}">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH" />
                    <div class="mb-3">
                      <label class="form-label">Nom du rôle</label>
                      <input class="form-control form-control-lg border-0" name="nom" value="{{$role->nom}}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Description</label>
                      <textarea class="form-control form-control-lg border-0 form-control-textarea" name="description" rows="2">{{ $role->description }}</textarea>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Permissions</label>
                      <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed text-heading-6 gy-5">
                          <!--begin::Table body-->
                          <tbody class="text-gray-600">
                            <!--begin::Table row-->
                            <tr>
                              <td class="text-gray-800 font-weight-bold">
                                Accès Administrateur
                                <i class="fal fa-exclamation-circle ml-1 fs-7" data-toggle="tooltip" title="Les Administrateurs ont toutes les permissions sur tout le système. Veuillez utiliser ce Rôle avec beaucoup de précautions."></i>
                              </td>
                              <td>
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid mr-9">
                                  <input class="form-check-input" type="checkbox" value="" id="roles_select_all2" />
                                  <span class="form-check-label" for="roles_select_all2">Tout sélectionner</span>
                                </label>
                                <!--end::Checkbox-->
                              </td>
                            </tr>
                            <!--end::Table row-->
                              @foreach ($sections as $section)
                                <tr>
                                  <td class="text-gray-800 font-weight-bold">{{ $section }}</td>
                                  <td>
                                  <div class="d-flex">
                                    @foreach ($permissions->where('section', $section) as $perm)
                                      <label class="form-check form-check-sm form-check-custom form-check-solid mr-5 mr-lg-20">
                                        <input @if ($role->permissions->contains($perm->id)) checked @endif class="form-check-input" type="checkbox" value="{{$perm->id}}" name="permissions[]" />
                                        <span class="form-check-label">{{$perm->nom}}</span>
                                      </label>
                                    @endforeach
                                  </div>
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                          <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                      </div>
                    </div>
                    <div class="text-center pt-3">
                      <button type="reset" class="btn btn-danger mr-3" data-role-modal-action="delete" data-uri="{{route('roles.destroy', $role)}}">Supprimer le Rôle</button>
                      <button type="submit" class="btn btn-primary"> Enregistrer</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        @endcan

      </div>
      <!--end::Sidebar-->
      <!--begin::Content-->
      <div class="col-md-8">
        <!--begin::Card-->
        <div class="card card-flush mb-6 mb-xl-9">
          <!--begin::Card header-->
          <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title">
              <h4 class="d-flex align-items-center">
                Users Assigned
                <span class="text-gray-600 fs-6 ms-1">(14)</span>
              </h4>
            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
              <!--begin::Search-->
              <div class="d-flex align-items-center position-relative my-1">
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                  </svg>
                </span>
                <!--end::Svg Icon-->
                <input type="text" data-roles-table-filter="search" class="form-control form-control-solid pl-15" style="max-width:250px;" placeholder="Search Users" />
              </div>
              <!--end::Search-->
              <!--begin::Group actions-->
              <div class="d-flex justify-content-end align-items-center" data-view-roles-table-toolbar="selected">
                <div class="mr-5">
                  <span class="mr-2" data-view-roles-table-select="selected_count"></span>Selected
                </div>
                <button type="button" class="btn btn-danger" data-view-roles-table-select="delete_selected">Delete Selected</button>
              </div>
              <!--end::Group actions-->
            </div>
            <!--end::Card toolbar-->
          </div>
          <!--end::Card header-->
          <!--begin::Card body-->
          <div class="card-body pt-0">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed mb-0" id="roles_view_table">
              <!--begin::Table head-->
              <thead>
                <!--begin::Table row-->
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                  <th class="pr-2" style="max-width:10px;">
                    <div class="form-check form-check-sm form-check-custom form-check-solid mr-3">
                      <input class="form-check-input" type="checkbox" data-check="true" data-check-target="#roles_view_table .form-check-input" value="1" />
                    </div>
                  </th>
                  <th class="min-w-50px" style="min-width:50px;">ID</th>
                  <th class="min-w-150px" style="min-width:150px;">User</th>
                  <th class="min-w-125px" style="min-width:125px;">Joined Date</th>
                  <th class="text-end min-w-100px" style="min-width:100px;">Actions</th>
                </tr>
                <!--end::Table row-->
              </thead>
              <!--end::Table head-->
              <!--begin::Table body-->
              <tbody class="fw-bold text-gray-600">
                <tr>
                  <!--begin::Checkbox-->
                  <td>
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input" type="checkbox" value="1" />
                    </div>
                  </td>
                  <!--end::Checkbox-->
                  <!--begin::ID-->
                  <td>ID8955</td>
                  <!--begin::ID-->
                  <!--begin::User=-->
                  <td class="d-flex align-items-center">
                    <!--begin:: Avatar -->
                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                      <a href="view.html">
                        <div class="symbol-label">
                          <img src="150-1.jpg" alt="" class="w-100" />
                        </div>
                      </a>
                    </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column">
                      <a href="view.html" class="text-gray-800 text-hover-primary mb-1">Emma Smith</a>
                      <span>e.smith@kpmg.com.au</span>
                    </div>
                    <!--begin::User details-->
                  </td>
                  <!--end::user=-->
                  <!--begin::Joined date=-->
                  <td>20 Dec 2021, 5:20 pm</td>
                  <!--end::Joined date=-->
                  <!--begin::Action=-->
                  <td class="text-end">
                    <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                    <span class="svg-icon svg-icon-5 m-0">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                      </svg>
                    </span>
                    <!--end::Svg Icon--></a>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                      <!--begin::Menu item-->
                      <div class="menu-item px-3">
                        <a href="view.html" class="menu-link px-3">View</a>
                      </div>
                      <!--end::Menu item-->
                      <!--begin::Menu item-->
                      <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-roles-table-filter="delete_row">Delete</a>
                      </div>
                      <!--end::Menu item-->
                    </div>
                    <!--end::Menu-->
                  </td>
                  <!--end::Action=-->
                </tr>
              </tbody>
              <!--end::Table body-->
            </table>
            <!--end::Table-->
          </div>
          <!--end::Card body-->
        </div>
        <!--end::Card-->
      </div>
      <!--end::Content-->
    </div>
    <!--end::Layout-->
  </div>
@endsection
@section('specific-js')
  <script type="text/javascript">
  jQuery(function(){
    // UPDATE ROLE
    const up_element = document.getElementById('modal_update_role');
    const up_form = up_element.querySelector('#form_update_role');
    const modal = new bootstrap.Modal(up_element);
    const up_selectAll = up_form.querySelector('#roles_select_all2');
    up_selectAll.addEventListener('change', e => {
      // Apply check state to all checkboxes
      $('#form_update_role [type="checkbox"]').each(function(){
        $(this)[0].checked = e.target.checked
      })
    });

    // DELETE ROLE
    const deleteButton = up_element.querySelector('[data-role-modal-action="delete"]');
    deleteButton.addEventListener('click', e => {
      e.preventDefault();
      Swal.fire({
        text: "Souhaitez-vous vraiment supprimer ce Rôle ?",
        icon: "question",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Oui",
        cancelButtonText: "Non",
        customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: "btn btn-active-light"
        }
      }).then(function (result) {
        if (result.value) {
          // Show loading indication
          $(up_element).waitMe({...waitMe_config});
          //  Ajax submit form here
          $.ajax({
            url: $(deleteButton).data("uri"),
            type: 'post',
            dataType: "JSON",
            data: { _token: $('meta[name=csrf-token]').attr('content'), _method: 'DELETE' },
            error: function (error) {
              // Remove loading indication
              $(up_element).waitMe('hide');
              let txt = "";
              if (error.status == 422) {
                txt += "<div class='text-start'>";
                for (let m in error.responseJSON.errors) {
                  for (let n in error.responseJSON.errors[m]) {
                    txt += "- " + error.responseJSON.errors[m][n] + "<br>";
                  }
                }
                txt += "</div>";
              } else {
                txt = error.responseJSON.message;
              }
              swal.fire({
                html: txt,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "D'accord",
                customClass: {
                  confirmButton: "btn btn-danger",
                },
              });
            },
            success: function (data) {
              // Remove loading indication
              $(up_element).waitMe('hide');
              if (data.success) {
                swal
                .fire({
                  html:
                  typeof data.message != "undefined" && data.message.length
                  ? data.message
                  : "Terminé avec succès !",
                  icon: "success",
                  buttonsStyling: false,
                  confirmButtonText: "Continuer",
                  customClass: {
                    confirmButton: "btn btn-primary",
                  },
                })
                .then(function () {
                  modal.hide();
                  if (typeof data.redirect != "undefined")
                  document.location.href = data.redirect;
                });
              } else {
                swal.fire({
                  html: data.message,
                  icon: "error",
                  buttonsStyling: false,
                  confirmButtonText: "D'accord",
                  customClass: {
                    confirmButton: "btn btn-danger",
                  },
                });
              }
            },
          });
        }
      });
    });
  })
  </script>
@endsection
