<form wire:submit.prevent="store" id="form" enctype="multipart/form-data" class="form-xhr">
  @csrf

  @method('POST')
  @if (!empty(request('continue')))
  <input type="hidden" name="continue" value="{{ request('continue') }}" />
  @endif
  <div class="row">
    <div class="col-lg-8">
      <!-- titre et infos de l'image -->
      <div class="card">
        <div class="card-body">
          <div class="input-group mb-3">
            <div class="col-lg-12">
              <div class="input-group mb-3">
                <label class="input-group-text">Catégorie | service</label>
                <select wire:model='newImage.idService' class="form-select" name="categorie" id="categorie">
                  <option value="">Selectionner un service</option>
                  @foreach (App\Models\Service::where('actif', true)->get() as $s)
                  <option value="{{ $s->id }}">{{ $s->label }}</option>
                  @endforeach
                </select>
              </div>
            </div>

          </div>
          <div class="input-group mb-3">
            <label class="input-group-text" for="project-icon-input">Titre</label>
            <input wire:model='newImage.titre' type="text" class="form-control" id="project-icon-input" name="titre"
              required>
          </div>
        </div>
        <div class="text-end mb-4 text-center">
          <button type="submit" class="btn btn-success w-sm">Enregistrer</button>
        </div>
        <!-- end card body -->
      </div>
    </div>
    <!-- chargement de l'image -->
    <!-- end col -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <div id="repeater">
            <div data-repeater-list="attributs">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">Image</h5>
                </div>
                <div class="card-body">
                  <input wire:model='addPhoto' type="file" />
                </div>
              </div>

            </div>
          </div>
        </div>
        <!-- end card body -->
      </div>
      <!-- end card -->


    </div>
    <!-- end col -->
  </div>
</form>

<!-- pour lesimages de la catégorie selectionnée -->
<div class="col-lg-12 md-8">
  <div class="card">
    <!-- afficher les images  ici -->
    <div class="row">
      @forelse ($photos as $img)
<!-- Modal -->
<!-- <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-5 text-center">
        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
          colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
        <div class="mt-4 text-center">
          <h4>Vous &ecirc;tes sur le point de supprimer un article?</h4>
          <p class="text-muted fs-15 mb-4">Toutes les infos seront
            supprim&eacute;es de la Base de Donn&eacute;es.</p>
          <div class="hstack gap-2 justify-content-center remove">
            <button class="btn btn-link link-success fw-medium text-decoration-none" data-bs-dismiss="modal"><i
                class="ri-close-line me-1 align-middle"></i>
              Fermer</button>
            <button class="btn btn-danger" id="delete-record"><a
                href="{{ route('gallerie.deleteimg', $img->id) }}"> Oui,
                Supprimer </a> </button>
          </div>
          {{-- <a href="{{ route('configuration.create') }}"> --}}
        </div>
      </div>
    </div>
  </div>
</div> -->
<!--end modal -->
        <div class="col-4">
          <a data-lity> <img src="{{ $img->image }}" class="img-thumbnail" alt="image de la gallerie">
          </a>
          <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
            title="Supprimer">
            <a href="{{ route('gallerie.deleteimg', $img->id) }}"  data-toggle="tooltip"
              class="text-danger d-inline-block remove-item-btn" >
              <i class="ri-delete-bin-5-fill fs-16"></i>
            </a>
          </li>
        </div>

        @empty
        <p> La gallerie est vide</p>
      @endforelse
    </div>
  </div>
  <!-- end row -->
</div>