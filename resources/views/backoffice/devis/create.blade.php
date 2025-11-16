@extends('backoffice.layouts')
@section('title')
    Devis - Demande de Service #{{ $reservation->id }}
@endsection
@section('content')
  <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10">
    <div class="mb-6">
        <h2 class="mb-0 text-heading fs-22 lh-15">Devis</h2>
        <p class="mb-1">Demande de Service #{{ $reservation->id }}</p>
    </div>
    <form method="POST" @if($reservation->devis) action="{{ route('devis.update', $reservation->devis->id) }}" @else action="{{ route('reservations.devis.store', $reservation) }}" @endif class="form-xhr">
      @csrf
      @if($reservation->devis) @method('patch') @else @method('post') @endif
      @if(!empty(request('continue')))
        <input type="hidden" name="continue" value="{{ request('continue') }}" />
      @endif
      <div class="row mb-6">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
          <div class="card mb-6">
            <div class="card-body px-6 pt-6 pb-5">
              <h3 class="card-title mb-0 text-heading fs-22 lh-15">Créer / Modifier un devis</h3>
              <p class="card-text">Détails du devis</p>
              <div class="form-group mb-6">
                <label for="cout" class="text-heading">Coût<span class="text-danger">*</span> <span class="text-muted">(en FCFA)</span></label>
                <input class="form-control form-control-lg border-0" id="cout" name="cout" @if($reservation->devis) value="{{ $reservation->devis->cout }}" @else value="{{$reservation->prix}}" @endif required />
              </div>
              <div class="form-group mb-6">
                <label for="description" class="text-heading">Description</label>
                <input type="hidden" name="description" @if($reservation->devis) value="{{ $reservation->devis->description }}" @else value="" @endif />
                <textarea id="description">@if($reservation->devis){{ $reservation->devis->description }}@endif</textarea>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="debut_execution" class="text-heading">Date début d'exécution<span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-lg border-0" id="debut_execution" name="debut_execution" @if($reservation->devis) value="{{ date('Y-m-d', strtotime($reservation->devis->debut_execution)) }}" @endif required />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="fin_execution" class="text-heading">Date fin d'exécution <span class="text-muted">(estimation)</span></label>
                    <input type="date" class="form-control form-control-lg border-0" id="fin_execution" name="fin_execution" @if($reservation->devis && $reservation->devis->fin_execution) value="{{ date('Y-m-d', strtotime($reservation->devis->fin_execution)) }}" @endif />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-2"></div>
      </div>
      <div class="d-flex justify-content-center flex-wrap">
        <a href="{{ url()->previous() }}" class="btn btn-lg bg-hover-white border rounded-lg mb-3">Annuler</a>
        <button type="submit" class="btn btn-lg btn-primary ml-4 mb-3">Enregistrer</button>
      </div>
    </form>
  </div>
@endsection
@section('specific-js')
  <script src="/vendors/ckeditor/ckeditor-classic.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/25.0.0/classic/translations/fr.js"></script>

  <script type="text/javascript">
  "use strict";
  jQuery(function() {

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
