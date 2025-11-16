@extends('backoffice.layouts')
@section('title')
  Rôles
@endsection

@section('content')
  <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10">
    <div class="mb-6">
      <h2 class="mb-0 text-heading fs-22 lh-15">
        Rôles
        @can('create', App\Models\Role::class)
          <button type="button" class="btn btn-outline btn-outline-primary ml-6 ml-sm-0" data-toggle="modal" data-target="#modal_add_role">
            Créer un rôle
          </button>          
        @endcan
      </h2>
    </div>

    <div class="row">
      @foreach ($roles as $r)
        <div class="col-md-4 mb-6">
          <div class="card border-secondary card-flush h-md-100">
            <div class="card-header">
              <span class="card-title font-weight-bold text-heading-2 text-uppercase">{{ $r->nom }}</span>
            </div>
            <div class="card-body pt-1">
              @can('viewAny', App\Models\Role::class)
                <div class="text-small text-muted my-0 ">
                  Utilisateurs ayant ce Rôle : {{$r->users_count}}
                </div>                
              @endcan
              <!--begin::Permissions-->
              <div class="d-flex flex-column text-gray-600">
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
            <div class="card-footer flex-wrap pt-0">
              @can('view', $r)
                <a href="{{route('roles.show', $r)}}" class="btn btn-outline btn-outline-info my-1 mr-2">Voir le Rôle</a>
              @endcan
              @can('update', $r)
                <button type="button" class="btn btn-link my-1" data-toggle="modal" data-target="#modal_update_role" onclick="$(this).populateUpdateForm({{$r->id}});">Modifier</button>
              @endcan
            </div>
            <!--end::Card footer-->
          </div>
        </div>
      @endforeach
    </div>

    @can('create', App\Models\Role::class)      
      {{-- Modal add new Role --}}
      <div class="modal fade" id="modal_add_role" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Ajouter un rôle</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="post" id="form_add_role" class="form-xhr" action="{{ route('roles.store') }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Nom du rôle</label>
                  <input class="form-control form-control-lg border-0" name="nom" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea class="form-control form-control-lg border-0 form-control-textarea" name="description" rows="2"></textarea>
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
                              <input class="form-check-input" type="checkbox" value="" id="roles_select_all" />
                              <span class="form-check-label" for="roles_select_all">Tout sélectionner</span>
                            </label>
                            <!--end::Checkbox-->
                          </td>
                        </tr>
                        <!--end::Table row-->
                        @foreach ($sections as $section)
                          <!--begin::Table row-->
                          <tr>
                            <!--begin::Label-->
                            <td class="text-gray-800 font-weight-bold">{{$section}}</td>
                            <!--end::Label-->
                            <!--begin::Options-->
                            <td>
                              <!--begin::Wrapper-->
                              <div class="d-flex">
                                @foreach ($permissions->where('section', $section) as $perm)
                                  <!--begin::Checkbox-->
                                  <label class="form-check form-check-sm form-check-custom form-check-solid mr-5 mr-lg-20">
                                    <input class="form-check-input" type="checkbox" value="{{$perm->id}}" name="permissions[]" />
                                    <span class="form-check-label">{{$perm->nom}}</span>
                                  </label>
                                  <!--end::Checkbox-->
                                @endforeach
                              </div>
                              <!--end::Wrapper-->
                            </td>
                            <!--end::Options-->
                          </tr>
                          <!--end::Table row-->
                        @endforeach
                      </tbody>
                      <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                  </div>
                </div>
                <div class="text-center pt-3">
                  <button type="reset" class="btn btn-light mr-3" data-dismiss="modal" aria-label="Close">Annuler</button>
                  <button type="submit" class="btn btn-primary"> Enregistrer</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endcan
    
    {{-- Modal edit Role --}}
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
            <form id="form_update_role" class="form-xhr" method="POST" data-action="{{route('roles.update', '__id__')}}">
              @csrf
              <input type="hidden" name="_method" value="PATCH" />
              <div class="mb-3">
                <label class="form-label">Nom du rôle</label>
                <input class="form-control form-control-lg border-0" name="nom" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control form-control-lg border-0 form-control-textarea" name="description" rows="2"></textarea>
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
                    </tbody>
                    <!--end::Table body-->
                  </table>
                  <!--end::Table-->
                </div>
              </div>
              <div class="text-center pt-3">
                <button type="reset" class="btn btn-danger mr-3" data-role-modal-action="delete" data-uri="{{route('roles.destroy', '__id__')}}">Supprimer le Rôle</button>
                <button type="submit" class="btn btn-primary"> Enregistrer</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('specific-js')
  <script type="text/javascript">
  const Sections = @json($sections);
  const Roles = @json($roles);
  const Permissions = @json($permissions);

  jQuery(function(){
    $.fn.populateUpdateForm = function(id) {
      let role = Roles.filter(item => item.id == id)[0]
      $('tr.removable').remove()
      $('#form_update_role').attr('action', $('#form_update_role').data('action').replace(/__id__/g, role.id))
      $('[data-role-modal-action=delete]').data('uri', $('[data-role-modal-action=delete]').data('uri').replace(/__id__/g, role.id))
      $('#form_update_role [name=nom]').val(role.nom)
      $('#form_update_role [name=description]').val(role.description)
      for (let key in Sections) {
        let tpl = ''
        tpl += `
        <tr class="removable">
        <td class="text-gray-800 font-weight-bold">`+Sections[key]+`</td>
        <td>
        <div class="d-flex">`
        let Perms = Permissions.filter(p => p.section == Sections[key])
        for (let k in Perms) {
          let perm = Perms[k];
          tpl += `
          <label class="form-check form-check-sm form-check-custom form-check-solid mr-5 mr-lg-20">
          <input `+((role.permissions.filter(itm => itm.id == perm.id).length) ? `checked` : ``)+` class="form-check-input" type="checkbox" value="`+perm.id+`" name="permissions[]" />
          <span class="form-check-label">`+perm.nom+`</span>
          </label>`
        }

        tpl += `
        </div>
        </td>
        </tr>`
        $('#form_update_role table tbody').append($(tpl))
      }
    }


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

  const element = document.getElementById('modal_add_role');
  const form = element.querySelector('#form_add_role');
  const selectAll = form.querySelector('#roles_select_all');
  const allCheckboxes = form.querySelectorAll('[type="checkbox"]');
  // Handle check state
  selectAll.addEventListener('change', e => {
    // Apply check state to all checkboxes
    allCheckboxes.forEach(c => {
      c.checked = e.target.checked;
    });
  });
  </script>
@endsection
