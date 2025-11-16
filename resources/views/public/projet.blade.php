@extends("public.layouts")
@section('title', 'Projets')

@section('content')


    <section class="pb-8 page-title shadow">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pt-6 pt-lg-2 lh-15 pb-5">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Listing</li>
                </ol>
                <h1 class="fs-30 lh-1 mb-0 text-heading font-weight-600">Liste des projets</h1>
            </nav>
        </div>
    </section>
    <section class="pt-8 pb-11">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 order-2 order-lg-1 primary-sidebar sidebar-sticky" id="sidebar">
                    <div class="primary-sidebar-inner">
                        <div class="card mb-4">
                            <div class="card-body px-6 py-4">
                                <h4 class="card-title fs-16 lh-2 text-dark mb-3">Trouver une maison</h4>
                                <div class="primary-sidebar-inner">
                                    <div class="card border-0 widget-request-tour">
                                        <ul class="nav nav-tabs d-flex" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active px-3" data-toggle="tab" href="#schedule"
                                                    role="tab" aria-selected="true">Location </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link px-3" data-toggle="tab" href="#request-info" role="tab"
                                                    aria-selected="false">Vente
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="card-body px-sm-6 shadow-xxs-2 pb-5 pt-0">

                                            <div class="tab-content pt-1 pb-0 px-0 shadow-none">
                                                <div class="tab-pane fade show active" id="schedule" role="tabpanel">

                                                    <form>
                                                        <div class="form-group mt-6">
                                                            <label for="key-word" class="sr-only">Localisation</label>
                                                            <input type="text"
                                                                class="form-control form-control-lg border-0 shadow-none"
                                                                id="key-word" placeholder="Localisation">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="Type" class="sr-only">Type</label>
                                                            <select
                                                                class="form-control border-0 shadow-none form-control-lg selectpicker"
                                                                title="Type" data-style="btn-lg py-2 h-52"
                                                                id="Type">
                                                                <option>Appartement</option>
                                                                <option>Villa</option>
                                                                <option>Bureau</option>
                                                                <option>Magasin</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="Nombre de Chambres" class="sr-only">Nombre de Chambres</label>
                                                            <select
                                                                class="form-control border-0 shadow-none form-control-lg selectpicker"
                                                                title="Nombre de Chambres" data-style="btn-lg py-2 h-52" id="Nombre de Chambres">
                                                                <option>1</option>
                                                                <option>2</option>
                                                                <option>3</option>
                                                                <option>4</option>
                                                                <option>5</option>
                                                                <option>6</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mt-6">
                                                            <label for="Budget Loyer" class="sr-only">Budget Loyer</label>
                                                            <input type="number" min="0"
                                                                class="form-control form-control-lg border-0 shadow-none"
                                                                id="Budget Loyer" placeholder="Budget Loyer">
                                                        </div>

                                                        <div class="form-group mt-6">
                                                            <label for="Superficie" class="sr-only">Superficie</label>
                                                            <input type="number" min="0"
                                                                class="form-control form-control-lg border-0 shadow-none"
                                                                id="Superficie" placeholder="Superficie">
                                                        </div>
                                                        
                                                        <button type="submit"
                                                            class="btn btn-primary btn-lg btn-block shadow-none mt-4">Envoyer
                                                        </button>
                                                    </form>

                                                </div>
                                                <div class="tab-pane fade pt-5" id="request-info" role="tabpanel">
                                                    <form>
                                                        <div class="form-group mt-6">
                                                            <label for="key-word" class="sr-only">Localisation</label>
                                                            <input type="text"
                                                                class="form-control form-control-lg border-0 shadow-none"
                                                                id="key-word" placeholder="Localisation">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="Type" class="sr-only">Type</label>
                                                            <select
                                                                class="form-control border-0 shadow-none form-control-lg selectpicker"
                                                                title="Type" data-style="btn-lg py-2 h-52"
                                                                id="Type">
                                                                <option>Appartement</option>
                                                                <option>Villa</option>
                                                                <option>Bureau</option>
                                                                <option>Magasin</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="Nombre de Chambres" class="sr-only">Nombre de Chambres</label>
                                                            <select
                                                                class="form-control border-0 shadow-none form-control-lg selectpicker"
                                                                title="Nombre de Chambres" data-style="btn-lg py-2 h-52" id="Nombre de Chambres">
                                                                <option>1</option>
                                                                <option>2</option>
                                                                <option>3</option>
                                                                <option>4</option>
                                                                <option>5</option>
                                                                <option>6</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mt-6">
                                                            <label for="Budget" class="sr-only">Budget </label>
                                                            <input type="number" min="0"
                                                                class="form-control form-control-lg border-0 shadow-none"
                                                                id="Budget" placeholder="Budget">
                                                        </div>

                                                        <div class="form-group mt-6">
                                                            <label for="Superficie" class="sr-only">Superficie</label>
                                                            <input type="number" min="0"
                                                                class="form-control form-control-lg border-0 shadow-none"
                                                                id="Superficie" placeholder="Superficie">
                                                        </div>
                                                        
                                                        <button type="submit"
                                                            class="btn btn-primary btn-lg btn-block shadow-none mt-4">Envoyer
                                                        </button>
                                                    </form>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card property-widget mb-4">
                            <div class="card-body px-6 pt-5 pb-6">
                                <h4 class="card-title fs-16 lh-2 text-dark mb-3">Featured Properties</h4>
                                <div class="slick-slider mx-0" data-slick-options='{"slidesToShow": 1, "autoplay":true}'>
                                    <div class="box px-0">
                                        <div class="card border-0">
                                            <img src="images/feature-property-01.jpg" class="card-img" alt="Villa on Hollywood
                                                        Boulevard">
                                            <div class="card-img-overlay d-flex flex-column bg-gradient-3 rounded-lg">
                                                <div class="d-flex mb-auto">
                                                    <a href="#" class="mr-1 badge badge-orange">featured</a>
                                                    <a href="#" class="badge badge-indigo">for Rent</a>
                                                </div>
                                                <div class="px-2 pb-2">
                                                    <a href="single-property-1.html" class="text-white">
                                                        <h5 class="card-title fs-16 lh-2 mb-0">Villa on Hollywood
                                                            Boulevard</h5>
                                                    </a>
                                                    <p class="card-text text-gray-light mb-0 font-weight-500">1421 San
                                                        Predro
                                                        St, Los Angeles</p>
                                                    <p class="text-white mb-0"><span class="fs-17 font-weight-bold">$2500
                                                        </span>/month
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box px-0">
                                        <div class="card border-0">
                                            <img src="images/feature-property-01.jpg" class="card-img" alt="Villa on Hollywood
                                                        Boulevard">
                                            <div class="card-img-overlay d-flex flex-column bg-gradient-3 rounded-lg">
                                                <div class="d-flex mb-auto">
                                                    <a href="#" class="mr-1 badge badge-orange">featured</a>
                                                    <a href="#" class="badge badge-indigo">for Rent</a>
                                                </div>
                                                <div class="px-2 pb-2">
                                                    <a href="single-property-1.html" class="text-white">
                                                        <h5 class="card-title fs-16 lh-2 mb-0">Villa on Hollywood
                                                            Boulevard</h5>
                                                    </a>
                                                    <p class="card-text text-gray-light mb-0 font-weight-500">1421 San
                                                        Predro
                                                        St, Los Angeles</p>
                                                    <p class="text-white mb-0"><span class="fs-17 font-weight-bold">$2500
                                                        </span>/month
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box px-0">
                                        <div class="card border-0">
                                            <img src="images/feature-property-01.jpg" class="card-img" alt="Villa on Hollywood
                                                        Boulevard">
                                            <div class="card-img-overlay d-flex flex-column bg-gradient-3 rounded-lg">
                                                <div class="d-flex mb-auto">
                                                    <a href="#" class="mr-1 badge badge-orange">featured</a>
                                                    <a href="#" class="badge badge-indigo">for Rent</a>
                                                </div>
                                                <div class="px-2 pb-2">
                                                    <a href="single-property-1.html" class="text-white">
                                                        <h5 class="card-title fs-16 lh-2 mb-0">Villa on Hollywood
                                                            Boulevard</h5>
                                                    </a>
                                                    <p class="card-text text-gray-light mb-0 font-weight-500">1421 San
                                                        Predro
                                                        St, Los Angeles</p>
                                                    <p class="text-white mb-0"><span class="fs-17 font-weight-bold">$2500
                                                        </span>/month
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mb-8 mb-lg-0 order-1 order-lg-2">
                    <div class="row align-items-sm-center mb-6">
                        <div class="col-md-6">
                            <h2 class="fs-15 text-dark mb-0">We found <span class="text-primary">45</span> properties
                                available for
                                you
                            </h2>
                        </div>
                        <div class="col-md-6 mt-6 mt-md-0">
                            <div class="d-flex justify-content-md-end align-items-center">
                                <div class="input-group border rounded input-group-lg w-auto bg-white mr-3">
                                    <label
                                        class="input-group-text bg-transparent border-0 text-uppercase letter-spacing-093 pr-1 pl-3"
                                        for="inputGroupSelect01"><i
                                            class="fas fa-align-left fs-16 pr-2"></i>Sortby:</label>
                                    <select
                                        class="form-control border-0 bg-transparent shadow-none p-0 selectpicker sortby"
                                        data-style="bg-transparent border-0 font-weight-600 btn-lg pl-0 pr-3"
                                        id="inputGroupSelect01" name="sortby">
                                        <option selected>Top Selling</option>
                                        <option value="1">Most Viewed</option>
                                        <option value="2">Price(low to high)</option>
                                        <option value="3">Price(high to low)</option>
                                    </select>
                                </div>
                                <div class="d-none d-md-block">
                                    <a class="fs-sm-18 text-dark opacity-2" href="/SingleProjet">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    <a class="fs-sm-18 text-dark ml-5" href="#">
                                        <i class="fa fa-th-large"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-35.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-primary">For Sale</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="/SingleProjet"
                                            class="text-dark hover-primary">Home in Metric Way</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $1.250.000
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-36.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-indigo">for Rent</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="/SingleProjet"
                                            class="text-dark hover-primary">Villa on Hollywood Boulevard</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $550
                                        <span class="fs-14 font-weight-500 text-gray-light"> /month</span>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-37.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-primary">For Sale</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="single-property-1.html"
                                            class="text-dark hover-primary">Affordable Urban House</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $1.250.000
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-73.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-primary">For Sale</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="single-property-1.html"
                                            class="text-dark hover-primary">Explore Old Barcelona</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $1.250.000
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-67.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-primary">For Sale</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="single-property-1.html"
                                            class="text-dark hover-primary">Home in Metric Way</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $1.250.000
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-68.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-indigo">for Rent</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="single-property-1.html"
                                            class="text-dark hover-primary">Garden Gingerbread House</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $550
                                        <span class="fs-14 font-weight-500 text-gray-light"> /month</span>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-69.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-primary">For Sale</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="single-property-1.html"
                                            class="text-dark hover-primary">Home in Metric Way</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $1.250.000
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-70.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-indigo">for Rent</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="single-property-1.html"
                                            class="text-dark hover-primary">Home in Metric Way</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $550
                                        <span class="fs-14 font-weight-500 text-gray-light"> /month</span>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-71.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-primary">For Sale</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="single-property-1.html"
                                            class="text-dark hover-primary">Home in Metric Way</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $1.250.000
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="card border-0" data-animate="fadeInUp">
                                <div class="position-relative hover-change-image bg-hover-overlay rounded-lg card-img">
                                    <img src="images/properties-grid-72.jpg" alt="Home in Metric Way">
                                    <div class="card-img-overlay d-flex flex-column">
                                        <div><span class="badge badge-primary">For Sale</span></div>
                                        <div class="mt-auto d-flex hover-image">
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                                                <li class="list-inline-item mr-2" data-toggle="tooltip" title="9 Images">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-images"></i><span
                                                            class="pl-1">9</span>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-toggle="tooltip" title="2 Video">
                                                    <a href="#" class="text-white hover-primary">
                                                        <i class="far fa-play-circle"></i><span
                                                            class="pl-1">2</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Wishlist">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mr-3 h-32" data-toggle="tooltip"
                                                    title="Compare">
                                                    <a href="#" class="text-white fs-20 hover-primary">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-3 px-0 pb-1">
                                    <h2 class="fs-16 mb-1"><a href="single-property-1.html"
                                            class="text-dark hover-primary">Home in Metric Way</a>
                                    </h2>
                                    <p class="font-weight-500 text-gray-light mb-0">1421 San Pedro St, Los Angeles</p>
                                    <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                        $1.250.000
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent px-0 pb-0 pt-2">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bedroom">
                                            <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-bedroom"></use>
                                            </svg>
                                            3 Br
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13 mr-sm-7"
                                            data-toggle="tooltip" title="3 Bathrooms">
                                            <svg class="icon icon-shower fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-shower"></use>
                                            </svg>
                                            3 Ba
                                        </li>
                                        <li class="list-inline-item text-gray font-weight-500 fs-13" data-toggle="tooltip"
                                            title="Size">
                                            <svg class="icon icon-square fs-18 text-primary mr-1">
                                                <use xlink:href="#icon-square"></use>
                                            </svg>
                                            2300 Sq.Ft
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <nav class="pt-4">
                        <ul class="pagination rounded-active justify-content-center mb-0">
                            <li class="page-item"><a class="page-link" href="#"><i
                                        class="far fa-angle-double-left"></i></a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item active"><a class="page-link" href="#">2</a></li>
                            <li class="page-item d-none d-sm-block"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">...</li>
                            <li class="page-item"><a class="page-link" href="#">6</a></li>
                            <li class="page-item"><a class="page-link" href="#"><i
                                        class="far fa-angle-double-right"></i></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </section>



@endsection
