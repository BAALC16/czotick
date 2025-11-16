<form method="POST" action="{{route('configuration.update',$configuration)}}" enctype="multipart/form-data"
    class="form-xhr" wire:submit.prevent='edit'>
    @csrf
    @method('PATCH')
    {{-- <input type="hidden" name="_method" @if($edit) value="patch" @else value="post" @endif /> --}}

    @if(!empty(request('continue')))
    <input type="hidden" name="continue" value="{{ request('continue') }}" />
    @endif
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Configurations membres</h5>
                </div>
                <div class="card-body">
                    <div id="repeater">
                        <div data-repeater-list="attributs">
                            <div data-repeater-item="attributs_item">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3 input-group">
                                            <label class="input-group-text">Points par d&eacute;faut</label>
                                            <input class="form-control" type="number" name="bonusRegister" 
                                                wire:model='bonusRegister' />
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Configurations Annonces</h5>
                </div>
                <div class="card-body">
                    <div id="repeater">
                        <div data-repeater-list="attributs">

                            <div data-repeater-item="attributs_item">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3 input-group">
                                            <label class="input-group-text">Points par Annonce par jour </label>
                                            <input class="form-control" name="creditPostDaily"
                                                wire:model='creditPostDaily'/>


                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Configuration actuelle</h5>
                </div>
                <div class="card-body">
                    <div id="repeater">
                        <div id="repeater">
                            <div data-repeater-list="attributs">
    
                                <div data-repeater-item="attributs_item">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3 input-group">
                                                <label class="input-group-text">Points par Annonce par jour </label>
                                                <input class="form-control" name="creditPostDaily"
                                                   value="{{$configuration->creditPostDaily}}" disabled/>
    
    
                                            </div>
                                        </div>
                                    </div>
                                </div>
    
                            </div>
                        </div>
                        <div id="repeater">
                            <div data-repeater-list="attributs">
                                <div data-repeater-item="attributs_item">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3 input-group">
                                                <label class="input-group-text">Points par d&eacute;faut</label>
                                                <input class="form-control" type="number" name="bonusRegister"
                                                value="{{$configuration->bonusRegister}}" disabled/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
    
                            </div>
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>

        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-success w-sm">Enregistrer</button>
        </div>
        <!-- end row -->
</form>