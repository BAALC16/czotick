<div class="col-lg-4">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Détails et facturation</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Informations &agrave; fournir &agrave; la demande du service.</p>
            <div id="repeater">
                <div data-repeater-list="attributs">
                   
                   
                  
                   
                    <div data-repeater-item="attributs_item">
                        <div class="row">
                            <div class="col-10">
                                <div class="mb-3 input-group">
                                    <label class="input-group-text">Dur&eacute;e (jours)</label>
                                    <input class="form-control" type="number" name="nbrJours" wire:model='nbrJours' />
                                </div>
                            </div>
                           
                            {{-- <div class="col-2">
                                <div class="mb-3 d-flex">
                                    <div class="flex-column">
                                        <a href="javascript:;" data-repeater-delete
                                            class="ml-3 mt-7 btn btn-outline btn-outline-danger">
                                            <i class="ri-delete-bin-5-fill fs-12"></i>
                                        </a>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <div>
                            <div class="input-group  mb-3">
                                <label class="input-group-text">Catégorie | Service</label>
                                <select class="form-select " wire:model='service_id'  name="service_id">
                                    <option value="">------------------------</option>
                                    @foreach (App\Models\Service::all() as $service)
                                    <option value="{{ $service->id }}">{{ $service->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="input-group mb-3">
                                <label class="input-group-text">Nbre de points/Annonce/Jours</label>
                                <input class="form-control"  type="number" wire:model='costPostDaily' name="costPostDaily" disabled />
                            </div>
                        </div>
                        <div>
                            <div class="input-group mb-3">
                                <label class="input-group-text">Total de points à depenser</label>
                                <input class="form-control" type="number" wire:model="total" name="total" disabled />
                            </div>
                        </div>
                    </div>
                   
                </div>
                <!-- <div>
                                        <a href="javascript:;" data-repeater-create class="btn btn-outline btn-outline-primary">
                                            <i class="fal fa-plus"></i> Ajouter un attribute
                                        </a>
                                    </div> -->

            </div>
        </div>
        <!-- end card body -->
    </div>
    <!-- end card -->


</div>