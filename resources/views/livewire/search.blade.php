<div>

    <form>
        <div class="input-group mxw-670 shadow-xxs-2 custom-input-group mb-2">
            <div class="input-group-prepend">
                <button class="btn shadow-none text-dark fs-18" type="button"><i class="fal fa-search"></i>
                </button>
            </div>
            <input type="text" wire:model="query" class="form-control bg-white shadow-none border-0 z-index-1"
                name="search" placeholder="Tapez votre recherche ici">
            {{-- <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Chercher</button>
            </div> --}}
        </div>
    </form>

    @if (strlen($query) > 2)
        <div class="mxw-670" style="position: relative !important;">
            <div class="search shadow rounded-lg mxw-670 p-3" style="position: absolute; z-index: 1000; background-color: #fff;">
                @if ($sugProperties != null)
                    <div class="input-group mxw-670   custom-input-group">
                        <p>Cherchez  << {{$query}}  >> dans  <a href="{{route('public.answers-properties',$query)}}">  les propriétés </a> </p>
                    </div>
                @else
                    <div class="input-group mxw-670  custom-input-group">
                        <p>0 résultat de << {{$query}}  >> dans les propriétés </p>
                    </div>
                @endif
                @if ($sugServices != null)
                    <div class="input-group mxw-670   custom-input-group">
                        <p>Cherchez  << {{$query}}  >> dans  <a href="{{route('public.answers-services',$query)}}">  les services </a> </p>
                    </div>
                @else
                    <div class="input-group mxw-670  custom-input-group">
                        <p>0 résultat de << {{$query}}  >> dans les services </p>
                    </div>
                @endif
                @if ($sugArticles != null)
                    <div class="input-group mxw-670 custom-input-group">
                        <p>Cherchez  << {{$query}}  >> dans  <a href={{route('public.answers-posts',$query)}}>  les articles </a> </p>
                    </div>
                @else
                    <div class="input-group mxw-670   custom-input-group">
                        <p>0 résultat de << {{$query}}  >> dans les articles </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>

@livewireScripts
