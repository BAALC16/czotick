@extends('backoffice.layouts')
@section('title')
  @if($edit) {{ $service->label }} @else Ajouter un service @endif
@endsection
@section('specific-css')
    <link href="/vendors/dropify/css/dropify.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
  <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10">
    <div class="mb-6">
      @if($edit)
        <h2 class="mb-0 text-heading fs-22 lh-15">{{ $service->label }}</h2>
        <p class="mb-1">Modifiez les détails ci-dessous et soumettez le formulaire pour enregistrer.</p>
      @else
        <h2 class="mb-0 text-heading fs-22 lh-15">Ajouter un service</h2>
        <p class="mb-1">Remplissez et soumettez le formulaire ci-dessous pour ajouter des services.</p>
      @endif
    </div>
    <form method="POST" @if($edit) action="{{ route('services.update', $service) }}" @else action="{{ route('services.store') }}" @endif enctype="multipart/form-data" class="form-xhr">
      @csrf
      <input type="hidden" name="_method" @if($edit) value="patch" @else value="post" @endif />
      @if(!empty(request('continue')))
        <input type="hidden" name="continue" value="{{ request('continue') }}" />
      @endif
      <div class="row mb-6">
        <div class="col-md-4">
          <div class="card mb-6">
            <div class="card-body px-6 pt-6 pb-5">
              <div class="row">
                <div class="col-lg-4 col-xl-12 col-xxl-7 mb-6">
                  <h3 class="card-title mb-0 text-heading fs-22 lh-15">Image</h3>
                  <p class="card-text">Associer une image au service. Taille recommandée : 483x539 px</p>
                </div>
                <div class="col-lg-8 col-xl-12 col-xxl-5">
                  <input type="file" name="image_file" accept="image/*" class="dropify" multiple data-show-loader="true" data-allowed-file-extensions="jpg jpeg png gif svg" data-max-file-size="20M" @if($edit) data-default-file="{{ $service->image_url }}" @endif />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card mb-6">
            <div class="card-body px-6 pt-6 pb-5">
              <h3 class="card-title mb-0 text-heading fs-22 lh-15">Détails du service</h3>
              <p class="card-text">Informations de base</p>
              <div class="form-group">
                <label for="label" class="text-heading">Libellé<span class="text-danger">*</span></label>
                <input class="form-control form-control-lg border-0" id="label" name="label" @if($edit) value="{{ $service->label }}" @endif required />
              </div>
              <div class="form-group">
                <label for="prix" class="text-heading">Coût <span class="text-muted">(en FCFA)</span></label>
                <input class="form-control form-control-lg border-0" id="prix" name="prix" @if($edit) value="{{ $service->prix }}" @endif />
              </div>
              <div class="mb-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" @if(!$edit || ($edit && $service->actif)) checked @endif name="actif" value="1" id="defaultCheck1">
                  <label class="form-check-label" for="defaultCheck1">
                    Publier
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="text-heading">Description</label>
                <input type="hidden" name="description" @if($edit) value="{{ $service->description }}" @endif />
                <textarea id="description" rows="8">@if($edit){{ $service->description }}@endif</textarea>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-body px-6 pt-6 pb-5">
              <h3 class="card-title mb-0 text-heading fs-22 lh-15">Attributs</h3>
              <p class="card-text">Les attributs représentent les informations à fournir à la demande du service.</p>

              <div id="repeater">
                <div data-repeater-list="attributs">
                  @if($edit && $service->attributs->isNotEmpty())
                    @foreach ($service->attributs as $item)
                      <div data-repeater-item="attributs_item" class="mb-5">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="text-heading">Libellé</label>
                              <input class="form-control form-control-lg border-0" name="label" value="{{ $item->label }}" />
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="d-flex">
                              <div class="flex-column flex-grow-1">
                                <div class="form-group">
                                  <label>Type de champ</label>
                                  <select class="form-control form-control-lg border-0" name="type_champ">
                                    @foreach (App\Models\Attribut::types_champ as $key => $value)
                                      <option value="{{ $key }}" @if($item->type_champ == $key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                  </select>
                                </div>
                              </div>
                              <div class="flex-column">
                                <a href="javascript:;" data-repeater-delete class="ml-3 mt-7 btn btn-outline btn-outline-danger"><i class="fal fa-trash-alt"></i></a>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-12 pt-0">
                            <div class="form-group">
                              <label class="text-heading">Description</label>
                              <input class="form-control form-control-lg border-0" name="description" value="{{ $item->description }}" />
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  @else
                    <div data-repeater-item="attributs_item" class="mb-5">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="text-heading">Libellé</label>
                            <input class="form-control form-control-lg border-0" name="label" />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="d-flex">
                            <div class="flex-column flex-grow-1">
                              <div class="form-group">
                                <label>Type de champ</label>
                                <select class="form-control form-control-lg border-0" name="type_champ">
                                  @foreach (App\Models\Attribut::types_champ as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                  @endforeach
                                </select>
                              </div>
                            </div>
                            <div class="flex-column">
                              <a href="javascript:;" data-repeater-delete class="ml-3 mt-7 btn btn-outline btn-outline-danger"><i class="fal fa-trash-alt"></i></a>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12 pt-0">
                          <div class="form-group">
                            <label class="text-heading">Description</label>
                            <input class="form-control form-control-lg border-0" name="description" />
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
                <div class="pt-4">
                  <a href="javascript:;" data-repeater-create class="btn btn-outline btn-outline-primary">
                    <i class="fal fa-plus"></i> Ajouter un attribute
                  </a>
                </div>

              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-end flex-wrap">
        <a href="{{ url()->previous() }}" class="btn btn-lg bg-hover-white border rounded-lg mb-3">Annuler</a>
        <button type="submit" class="btn btn-lg btn-primary ml-4 mb-3">Enregistrer</button>
      </div>
    </form>
  </div>
@endsection
@section('specific-js')
  <script src="/vendors/formrepeater/formrepeater.js"></script>
  <script src="/vendors/ckeditor/ckeditor-classic.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/25.0.0/classic/translations/fr.js"></script>
  <script src="/vendors/dropify/js/dropify.min.js"></script>

  <script type="text/javascript">
  "use strict";
  jQuery(function() {
    $('.dropify').dropify({...dropify_config})

    ClassicEditor.create(document.querySelector('#description'), {
      language: 'fr',
      ckfinder: {
        uploadUrl: "{{ route('media.upload-image', ['_token' => csrf_token()]) }}&CKEditorFuncNum=1&CKEditor=description"
      }
    })
    .then(editor => {
      editor.model.document.on('change:data', () => {
        $('input[name=description]').val(editor.getData())
      });
    })
    .catch(error => {
      console.error(error);
    })
    $('#repeater').repeater({
      initEmpty: false,
      show: function() {
        $(this).slideDown();
      },

      hide: function(deleteElement) {
        $(this).slideUp(deleteElement);
      },

      ready: function() {
        console.log("Repeater loaded.");
      }
    });
  })
  </script>
@endsection
