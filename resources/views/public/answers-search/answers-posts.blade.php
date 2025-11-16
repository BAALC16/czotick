@extends("public.layouts")
@section('specific-css')
<link href="{{ asset('assets/blog/blogstyle.css') }}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="/vendors/styleposts.css">
<link rel="stylesheet" href="/vendors/stylesearchbar.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    .iti {
        width: 100%;
    }

    .box img {
        width: 100%;
        height: 100%;
    }
</style>
@endsection
@section("title", "Annonces")

@section("content")


<main id="content">
    <section class="pt-2 pb-13 page-title bg-img-cover-center bg-white-overlay"
        style="background-image: url('images/bg-title.jpg');">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="#">ACCUEIL</a></li>
                    <li class="breadcrumb-item active" aria-current="page">BLOG</li>
                </ol>
            </nav>
            <h1 class="fs-30 lh-15 mb-0 text-heading font-weight-500 text-center pt-10" data-animate="fadeInDown">
                Retrouvez les annonces qui vous conviennent le plus</h1>
        </div>
    </section>
    <section class="pt-11 pb-13">
        <div class="container">
            <div class="row ml-xl-0 mr-xl-n6">
                <div class="col-lg-8 mb-8 mb-lg-0 pr-xl-6 pl-xl-0">
                    @foreach ($suggestions as $suggestion )
                    <div class="card border-0 pb-6 mb-6 border-bottom">
                        <div class="position-relative d-flex align-items-end card-img-top">
                            <a href="{{route('public.single-page',$suggestion->id)}}" class="hover-shine d-block">
                                <img src="storage/{{$suggestion->image}}" alt="{{$suggestion->title}}">
                            </a>
                            <a href="#"
                                class="badge text-white bg-dark-opacity-04 fs-13 font-weight-500 bg-hover-primary hover-white m-2 position-absolute letter-spacing-1 pos-fixed-bottom">
                                rental
                            </a>
                        </div>

                        <div class="card-body p-0">
                            <ul class="list-inline mt-4">
                                <li class="list-inline-item mr-4"><img class="mr-1" src="images/author-01.jpg"
                                        alt="D. Warren"> D. Warren
                                </li>
                                <li class="list-inline-item mr-4"><i class="far fa-calendar mr-1"></i> {{
                                    Carbon::parse($suggestion->created_at)->isoFormat("DD MMM YYYY") }}
                                </li>
                                <li class="list-inline-item mr-4"><i class="far fa-eye mr-1"></i> 149 views
                                </li>
                            </ul>
                            <h3 class="fs-md-32 text-heading lh-141 mb-3">
                                <a href="{{route('public.single-page',$suggestion->id)}}"
                                    class="text-heading hover-primary">{{$suggestion->title}}</a>
                            </h3>
                            <p class="mb-4 lh-214">{{$suggestion->chapo}}</p>
                        </div>
                        <div class="card-footer bg-transparent p-0 border-0">
                            <a href="{{route('public.single-page',$suggestion->id)}}"
                                class="btn text-heading border btn-lg shadow-none btn-outline-light border-hover-light">Lire
                                la suite <i class="far fa-long-arrow-right text-primary ml-1"></i></a>
                            <a href="#"
                                class="btn text-heading btn-lg w-52px px-2 border shadow-none btn-outline-light border-hover-light rounded-circle ml-auto float-right"><i
                                    class="fad fa-share-alt text-primary"></i></a>
                        </div>
                    </div>
                    @endforeach

                    <!-- fin les autres annones en miniatures -->
                    <nav class="pt-4">
                        {{-- <ul class="pagination rounded-active justify-content-center">
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
                        </ul> --}}
                        {{$suggestions->links()}}
                    </nav>
                </div>
                <div class="col-lg-4 pl-xl-6 pr-xl-0 primary-sidebar sidebar-sticky" id="sidebar">
                    <div class="primary-sidebar-inner">
                        <div class="card mb-4">
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
                        </div>
                        {{-- à mettre  --}}
                        <div class="card mb-4">
                            <div class="card-body px-6 pt-5 pb-6">
                                <h4 class="card-title fs-16 lh-2 text-dark mb-3">Catégories</h4>
                                <ul class="list-group list-group-no-border">
                                    <li class="list-group-item p-0">
                                        <a href="listing-with-left-sidebar.html" class="d-flex text-body hover-primary">
                                            <span class="lh-29">Creative</span>
                                            <span class="d-block ml-auto">13</span>
                                        </a>
                                    </li>
                                    <li class="list-group-item p-0">
                                        <a href="listing-with-left-sidebar.html" class="d-flex text-body hover-primary">
                                            <span class="lh-29">Rentals</span>
                                            <span class="d-block ml-auto">21</span>
                                        </a>
                                    </li>
                                    <li class="list-group-item p-0">
                                        <a href="listing-with-left-sidebar.html" class="d-flex text-body hover-primary">
                                            <span class="lh-29">Images and B-Roll</span>
                                            <span class="d-block ml-auto">17</span>
                                        </a>
                                    </li>
                                    <li class="list-group-item p-0">
                                        <a href="listing-with-left-sidebar.html" class="d-flex text-body hover-primary">
                                            <span class="lh-29">In the News</span>
                                            <span class="d-block ml-auto">4</span>
                                        </a>
                                    </li>
                                    <li class="list-group-item p-0">
                                        <a href="listing-with-left-sidebar.html" class="d-flex text-body hover-primary">
                                            <span class="lh-29">Real Estate</span>
                                            <span class="d-block ml-auto">27</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-body px-6 pt-5 pb-6">
                                <h4 class="card-title fs-16 lh-2 text-dark mb-3">Articles recents</h4>
                                <ul class="list-group list-group-flush">
                                    @foreach ($postsRecents as $postsRecent )
                                    <li class="list-group-item px-0 pt-0 pb-3">
                                        <div class="media">
                                            <div class="position-relative mr-3">
                                                <a href="{{route('public.single-page',$postsRecent->id)}}"
                                                    class="d-block w-100px rounded pt-11 bg-img-cover-center"
                                                    style="background-image: url('storage/{{$postsRecent->image}}')">
                                                </a>
                                                <a href="blog-grid-with-sidebar.html"
                                                    class="badge text-white bg-dark-opacity-04 m-1 fs-13 font-weight-500 bg-hover-primary hover-white position-absolute pos-fixed-top">
                                                    creative
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <h4 class="fs-14 lh-186 mb-1">
                                                    <a href="{{route('public.single-page',$postsRecent->id)}}" class="text-dark hover-primary">
                                                        {{$postsRecent->chapo }}
                                                    </a>
                                                </h4>
                                                <div class="text-gray-light">
                                                    {{$postsRecent->created_at }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                    {{-- <li class="list-group-item px-0 pt-2 pb-3">
                                        <div class="media">
                                            <div class="position-relative mr-3">
                                                <a href="blog-details-1.html"
                                                    class="d-block w-100px rounded pt-11 bg-img-cover-center"
                                                    style="background-image: url('images/post-04.jpg')">
                                                </a>
                                                <a href="blog-grid-with-sidebar.html"
                                                    class="badge text-white bg-dark-opacity-04 m-1 fs-13 font-weight-500 bg-hover-primary hover-white position-absolute pos-fixed-top">
                                                    rental
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <h4 class="fs-14 lh-186 mb-1">
                                                    <a href="blog-details-1.html" class="text-dark hover-primary">
                                                        Within the construction industry as their overdraft
                                                    </a>
                                                </h4>
                                                <div class="text-gray-light">
                                                    Dec 16, 2018
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item px-0 pt-2 pb-0">
                                        <div class="media">
                                            <div class="position-relative mr-3">
                                                <a href="blog-details-1.html"
                                                    class="d-block w-100px rounded pt-11 bg-img-cover-center"
                                                    style="background-image: url('images/post-07.jpg')">
                                                </a>
                                                <a href="blog-grid-with-sidebar.html"
                                                    class="badge text-white bg-dark-opacity-04 m-1 fs-13 font-weight-500 bg-hover-primary hover-white position-absolute pos-fixed-top">
                                                    rental
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <h4 class="fs-14 lh-186 mb-1">
                                                    <a href="blog-details-1.html" class="text-dark hover-primary">
                                                        Future Office Buildings: Intelligent by Design
                                                    </a>
                                                </h4>
                                                <div class="text-gray-light">
                                                    Dec 16, 2018
                                                </div>
                                            </div>
                                        </div>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                        <div class="card mb-4">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<script src="vendors/jquery.min.js"></script>
<script src="vendors/jquery-ui/jquery-ui.min.js"></script>
<script src="vendors/bootstrap/bootstrap.bundle.js"></script>
<script src="vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
<script src="vendors/slick/slick.min.js"></script>
<script src="vendors/waypoints/jquery.waypoints.min.js"></script>
<script src="vendors/counter/countUp.js"></script>
<script src="vendors/magnific-popup/jquery.magnific-popup.min.js"></script>
<script src="vendors/chartjs/Chart.min.js"></script>
<script src="vendors/dropzone/js/dropzone.min.js"></script>
<script src="vendors/timepicker/bootstrap-timepicker.min.js"></script>
<script src="vendors/hc-sticky/hc-sticky.min.js"></script>
<script src="vendors/jparallax/TweenMax.min.js"></script>
<script src="vendors/mapbox-gl/mapbox-gl.js"></script>
<script src="vendors/dataTables/jquery.dataTables.min.js"></script>

<script src="js/theme.js"></script>
<div class="modal fade login-register login-register-modal" id="login-register-modal" tabindex="-1" role="dialog"
    aria-labelledby="login-register-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mxw-571" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 p-0">
                <div class="nav nav-tabs row w-100 no-gutters" id="myTab" role="tablist">
                    <a class="nav-item col-sm-3 ml-0 nav-link pr-6 py-4 pl-9 active fs-18" id="login-tab"
                        data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">Login</a>
                    <a class="nav-item col-sm-3 ml-0 nav-link py-4 px-6 fs-18" id="register-tab" data-toggle="tab"
                        href="#register" role="tab" aria-controls="register" aria-selected="false">Register</a>
                    <div class="nav-item col-sm-6 ml-0 d-flex align-items-center justify-content-end">
                        <button type="button" class="close m-0 fs-23" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body p-4 py-sm-7 px-sm-8">
                <div class="tab-content shadow-none p-0" id="myTabContent">
                    <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                        <form class="form">
                            <div class="form-group mb-4">
                                <label for="username" class="sr-only">Username</label>
                                <div class="input-group input-group-lg">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18"
                                            id="inputGroup-sizing-lg">
                                            <i class="far fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control border-0 shadow-none fs-13" id="username"
                                        name="username" required placeholder="Username / Your email">
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="password" class="sr-only">Password</label>
                                <div class="input-group input-group-lg">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                                            <i class="far fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control border-0 shadow-none fs-13" id="password"
                                        name="password" required placeholder="Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-gray-01 border-0 text-body fs-18">
                                            <i class="far fa-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="remember-me"
                                        name="remember-me">
                                    <label class="form-check-label" for="remember-me">
                                        Remember me
                                    </label>
                                </div>
                                <a href="password-recovery.html" class="d-inline-block ml-auto text-orange fs-15">
                                    Lost password?
                                </a>
                            </div>
                            <div class="d-flex p-2 border re-capchar align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="verify" name="verify">
                                    <label class="form-check-label" for="verify">
                                        I'm not a robot
                                    </label>
                                </div>
                                <a href="#" class="d-inline-block ml-auto">
                                    <img src="images/re-captcha.png" alt="Re-capcha">
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Log in</button>
                        </form>
                        <div class="divider text-center my-2">
                            <span class="px-4 bg-white lh-17 text">
                                or continue with
                            </span>
                        </div>
                        <div class="row no-gutters mx-n2">
                            <div class="col-4 px-2 mb-4">
                                <a href="#" class="btn btn-lg btn-block facebook text-white px-0">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </div>
                            <div class="col-4 px-2 mb-4">
                                <a href="#" class="btn btn-lg btn-block google px-0">
                                    <img src="images/google.png" alt="Google">
                                </a>
                            </div>
                            <div class="col-4 px-2 mb-4">
                                <a href="#" class="btn btn-lg btn-block twitter text-white px-0">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                        <form class="form">
                            <div class="form-group mb-4">
                                <label for="full-name" class="sr-only">Full name</label>
                                <div class="input-group input-group-lg">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                                            <i class="far fa-address-card"></i></span>
                                    </div>
                                    <input type="text" class="form-control border-0 shadow-none fs-13" id="full-name"
                                        name="full-name" required placeholder="Full name">
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="username01" class="sr-only">Username</label>
                                <div class="input-group input-group-lg">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                                            <i class="far fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control border-0 shadow-none fs-13" id="username01"
                                        name="username01" required placeholder="Username / Your email">
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="password01" class="sr-only">Password</label>
                                <div class="input-group input-group-lg">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                                            <i class="far fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control border-0 shadow-none fs-13" id="password01"
                                        name="password01" required placeholder="Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-gray-01 border-0 text-body fs-18">
                                            <i class="far fa-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                                <p class="form-text">Minimum 8 characters with 1 number and 1 letter</p>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Sign up</button>
                        </form>
                        <div class="divider text-center my-2">
                            <span class="px-4 bg-white lh-17 text">
                                or continue with
                            </span>
                        </div>
                        <div class="row no-gutters mx-n2">
                            <div class="col-4 px-2 mb-4">
                                <a href="#" class="btn btn-lg btn-block facebook text-white px-0">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </div>
                            <div class="col-4 px-2 mb-4">
                                <a href="#" class="btn btn-lg btn-block google px-0">
                                    <img src="images/google.png" alt="Google">
                                </a>
                            </div>
                            <div class="col-4 px-2 mb-4">
                                <a href="#" class="btn btn-lg btn-block twitter text-white px-0">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </div>
                        </div>
                        <div class="mt-2">By creating an account, you agree to HomeID
                            <a class="text-heading" href="#"><u>Terms of Use</u> </a> and
                            <a class="text-heading" href="#"><u>Privacy Policy</u></a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('specific-js')

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="{{ asset('assets/intlinput/js/intlTelInput.js') }}"></script>
<script>
    var input = document.querySelector("#phone");
    var iti = window.intlTelInput(input, {
        hiddenInput: "phone",
        initialCountry: "ci",
        nationalMode: true,
        utilsScript: "{{asset('assets/intlinput/js/utils.js')}}",
    });
    var handleChange = function () {

    };

    // listen to "keyup", but also "change" to update when the user selects a country
    input.addEventListener('change', handleChange);
    input.addEventListener('keyup', handleChange);
</script>
@endsection