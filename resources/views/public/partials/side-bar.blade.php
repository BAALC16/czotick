<div class="col-lg-4 pl-xl-6 pr-xl-0 primary-sidebar sidebar-sticky" id="sidebar">
    <div class="primary-sidebar-inner">
        {{-- <div class="card mb-4">
            <div class="card-body px-6 pt-5 pb-6">
                <h4 class="card-title fs-16 lh-2 text-dark mb-3">Rechercher</h4>
                <form>
                    <div class="position-relative">
                        <input type="text" id="search02"
                            class="form-control form-control-lg border-0 shadow-none"
                            placeholder="Ecrivez ici..." name="search">
                        <div class="position-absolute pos-fixed-center-right">
                            <button type="submit" class="btn fs-15 text-dark shadow-none"><i
                                    class="fal fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div> --}}
        {{-- à mettre --}}
        <div class="card mb-4">
            <div class="card-body px-6 pt-5 pb-6">
                <h4 class="card-title fs-16 lh-2 text-dark mb-3">Categories</h4>
                <ul class="list-group list-group-no-border">
                    @forelse($categories as $category)
                        <li class="list-group-item p-0">
                            <a class="d-flex text-body hover-primary">
                            <span class="lh-29">{{ $category->label }}</span>
                            <span class="d-block ml-auto">{{ $category->articles->count() }}</span>
                            </a>
                        </li>
                    @empty

                    @endforelse
                </ul>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body px-6 pt-5 pb-6">
                <h4 class="card-title fs-16 lh-2 text-dark mb-3">Articles récents</h4>
                <ul class="list-group list-group-flush">
                    @forelse($postsRecents as $postsRecent)
                        <li class="list-group-item px-0 pt-0 pb-3">
                            <div class="media">
                                <div class="position-relative mr-3">
                                    <a href="blog-details-1.html" class="d-block w-100px rounded pt-11 bg-img-cover-center" style="background-image: url({{$postsRecent->image}})">
                                    </a>
                             
                                </div>
                                <div class="media-body">
                                    <h4 class="fs-14 lh-186 mb-1">
                                        <a href="blog-details-1.html" class="text-dark hover-primary">
                                            {{ $postsRecent->title}}
                                        </a>
                                    </h4>
                                    <div class="text-gray-light">
                                        {{ Carbon::parse($postsRecent->created_at)->isoFormat("DD MMM YYYY") }}
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        Aucun article disponible
                    @endforelse
                </ul>
            </div>
        </div>
        {{-- <div class="card mb-4">
            <div class="card-body px-6 py-5">
                <h4 class="card-title fs-16 lh-2 text-dark mb-3">Tags</h4>
                <ul class="list-inline mb-0">
                    <li class="list-inline-item mb-2">
                        <a href="#"
                            class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">designer</a>
                    </li>
                    <li class="list-inline-item mb-2">
                        <a href="#"
                            class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">mockup</a>
                    </li>
                    <li class="list-inline-item mb-2">
                        <a href="#"
                            class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">template</a>
                    </li>
                    <li class="list-inline-item mb-2">
                        <a href="#"
                            class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">IT
                            Security</a>
                    </li>
                    <li class="list-inline-item mb-2">
                        <a href="#"
                            class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">IT
                            services</a>
                    </li>
                    <li class="list-inline-item mb-2">
                        <a href="#"
                            class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">business</a>
                    </li>
                    <li class="list-inline-item mb-2">
                        <a href="#"
                            class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">videos</a>
                    </li>
                    <li class="list-inline-item mb-2">
                        <a href="#"
                            class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">wordpress
                            theme</a>
                    </li>
                    <li class="list-inline-item mb-2">
                        <a href="#"
                            class="px-2 py-1 d-block fs-13 lh-17 bg-gray-03 text-muted hover-white bg-hover-primary rounded">sketch</a>
                    </li>
                </ul>
            </div>
        </div> --}}
    </div>
</div>