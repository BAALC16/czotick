@extends('backoffice.layouts')
@section('title')
    Utilisateurs
@endsection
@section('specific-css')
  <link rel="stylesheet" href="/vendors/dataTables/jquery.dataTables.min.css">
@endsection
@section('content')
<div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10 user-listing">
  <div class="mb-6">
    <form action="{{route('users.index')}}" method="GET">
      <div class="row">
        <div class="col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center">
          <div class="d-flex form-group mb-0 align-items-center">
            <label for="user-list_length" class="d-block mr-2 mb-0">Afficher:</label>
            <select
                  name="perpage" id="user-list_length"
                  aria-controls="user-list" class="form-control form-control-lg mr-2 selectpicker"
                  data-style="bg-white btn-lg h-52 py-2 border">
                  @foreach([10,25,50,100] as $v)
                    <option value="{{$v}}" @if(request('perpage', 25) == $v) selected @endif>{{$v}}</option>
                  @endforeach
            </select>
          </div>
        </div>
        <div class="col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3">
          <div class="input-group input-group-lg bg-white mb-0 position-relative mr-2">
            <input type="text" name="q" class="form-control bg-transparent border-1x" placeholder="Chercher..."
                 aria-label=""
                 aria-describedby="basic-addon1" value="{{request('q')}}">
            <div class="input-group-append position-absolute pos-fixed-right-center">
              <button class="btn bg-transparent border-0 text-gray lh-1" type="submit"><i
                      class="fal fa-search"></i></button>
            </div>
          </div>
          @can('deleteMultiple', App\Models\User::class)
            <div class="align-self-center d-none">
              <button type="button" class="btn btn-danger btn-lg" id="delete-selection" data-href="{{route('users.destroy-multiple')}}" data-items="" data-group="users[]" tabindex="0"
                    aria-controls="users-list"><span>Supprimer</span></button>
            </div>            
          @endcan
        </div>
      </div>
    </form>
  </div>
  <div class="table-responsive">
    <table id="users-list" class="table table-hover bg-white border rounded-lg">
      <thead>
        <tr role="row">
          <th class="no-sort py-6 pl-6">
            <label
                class="new-control new-checkbox checkbox-primary m-auto">
              <input type="checkbox"
                   class="new-control-input chk-parent select-customers-info" />
            </label>
          </th>
          <th class="py-6">Nom</th>
          <th class="py-6">E-mail</th>
          <th class="py-6">Mobile</th>
          <th class="py-6">Titre</th>
          <th class="py-6">Ville</th>
          <th class="py-6">Date insc.</th>
          <th class="no-sort py-6">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          <tr role="row">
            <td class="checkbox-column py-2 pl-6">
              <label
                  class="new-control new-checkbox checkbox-primary m-auto">
                <input type="checkbox" name="users[]" value="{{$u->id}}" class="new-control-input child-chk select-customers-info" />
              </label>
            </td>
            <td class="align-middle">
              <div class="d-flex align-items-center">
                <div class="usr-img-frame mr-2 rounded-circle">
                  <img alt="avatar" class="img-fluid rounded-circle w-30px"
                          src="{{$u->photo_url}}">
                </div>
                <p class="align-self-center mb-0 user-name">
                  @can('view', $u)
                    <a href="{{route('users.show', $u)}}">{{$u->full_name}}</a>
                  @else
                    {{$u->full_name}}
                  @endcan
                </p>
              </div>
            </td>
            <td class="align-middle"><span class="text-primary pr-1"><i class="fal fa-envelope"></i></span>{{$u->email}}</td>
            <td class="align-middle"><span class="text-primary pr-1">@if($u->mobile)<i class="fal fa-phone"></i></span>{{$u->mobile}} @else - @endif</td>
            <td class="align-middle">{{$u->titre ?? 'Utilisateur'}}</td>
            <td class="align-middle">@if($u->ville) {{$u->ville}} @else - @endif</td>
            <td class="align-middle">{{Carbon\Carbon::parse($u->created_at)->isoFormat('DD MMM YYYY')}}</td>
            <td class="align-middle">
              @can('update', $u)
                <a href="{{route('users.edit', $u)}}" data-toggle="tooltip" title="Modifier"
                  class="d-inline-block fs-18 text-muted hover-primary mr-5"><i
                        class="fal fa-pencil-alt"></i></a>                
              @endcan
              @can('delete', $u)
                <a href="javascript:;" data-href="{{route('users.destroy', $u)}}" data-toggle="tooltip" title="Supprimer"
                  class="confirm d-inline-block fs-18 text-muted hover-primary click-to-delete-row" data-confirm="Souhaitez-vous vraiment supprimer cet utilisateur ?"><i
                        class="fal fa-trash-alt"></i></a>
              @endcan
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="py-6 text-center"><em>Il n'y a rien à afficher pour le moment.</em></td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-6">
    {{$users->withQueryString()->links()}}
  </div>
</div>
@endsection
@section('specific-js')
    <script src="/vendors/dataTables/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
      jQuery(function(){
        var $table = $('#users-list');
        $table.DataTable({
          "order": [],
          "paging": false,
          "searching": false,
          "info": false,
          "columnDefs": [
            { "orderable": false, "targets": 0 },
            {
            "targets": 'no-sort',
            "orderable": false
          } ]
        });
  
        $('#user-list_length').change(function(){
          $(this).closest('form')[0].submit();
        });

        @can('deleteMultiple', App\Models\User::class)          
          $(document).on('table-items-selected', function(e) {
            let selected_ids = [];
            for (let i = 0; i < e.parameters.items.length; i++) {
              const item = e.parameters.items[i];
              if(item.checked)
                selected_ids.push(item.value)
            }
            $('#delete-selection').data('items', selected_ids)
            if(selected_ids.length > 0) {
              $('#delete-selection').closest('div').removeClass('d-none')
            } else {
              $('#delete-selection').closest('div').addClass('d-none')
            }
          })

          $('#delete-selection').click(function() {
            let elt = $(this)
            if($(this).data('items') != "") {
              Swal.fire({
                text: "Souhaitez-vous vraiment supprimer ces utilisateurs ?",
                icon: "question",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Oui, je confirme",
                cancelButtonText: "Non, annuler",
                customClass: {
                  confirmButton: "btn font-weight-bold btn-danger",
                  cancelButton: "ml-3 btn font-weight-bold btn-primary",
                },
              }).then(function (result) {
                if (result.value) {
                  $('#content').waitMe({...waitMe_config});
                  $.post(elt.data('href'), {
                    '_token': $("meta[name=csrf-token]").attr("content"),
                    '_method': 'DELETE',
                    'items[]': elt.data('items')
                  })
                  .done((r) => {
                    window.location.reload(true);
                  })
                  .fail((er) => {
                    $('#content').waitMe('hide');
                    Swal.fire({
                      text: er.responseJSON.message,
                      icon: "error",
                      buttonsStyling: false,
                      confirmButtonText: "D'accord",
                      customClass: {
                        confirmButton: "btn font-weight-bold btn-danger",
                      },
                    });
                  });
                }
              });

            }
          })
        @endcan
      });
      
    </script>
@endsection
