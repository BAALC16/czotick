<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('backend') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/front/images/logo1.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/front/images/logo1.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('backend') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/front/images/logo1.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/front/images/logo1.png') }}" alt="" height="50">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                @if(Auth::user()->getIsAdmin())
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarDashboards-blog" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarDashboards-blog">
                            <i class="ri-article-line"></i> <span >Blog</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarDashboards-blog">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('articles.index') }}" nav="articles" class="nav-link" >Liste des articles</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('articles.create') }}" nav="articles/create" class="nav-link" >Créer un article</a>
                                </li>
                            </ul>
                        </div>
                    </li> <!-- end Dashboard Menu -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarActivity" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarActivity">
                            <i class="ri-apps-2-line"></i> <span >Activité</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarActivity">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('conventions.index') }}" nav="activities" class="nav-link" >Conventions</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('activities.index') }}" nav="activities" class="nav-link" >Liste des activité</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('activities.create') }}" nav="activities/create" class="nav-link" >Créer une activité</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('programs.index') }}" nav="programs" class="nav-link" >Programme</a>
                                </li>
                            </ul>
                        </div>
                    </li> <!-- end Dashboard Menu -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarPatner" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarPatner">
                            <i class="las la-hands-helping"></i> <span >Partenaire</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarPatner">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('patners.index') }}" nav="patners" class="nav-link" >Liste des partenaires</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('patners.create') }}" nav="patners/create" class="nav-link" >Créer un partenaire</a>
                                </li>
                            </ul>
                        </div>
                    </li> <!-- end Dashboard Menu -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarPresident" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarPresident">
                            <i class="ri-team-fill"></i> <span >PEL</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarPresident">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('presidents.index') }}" nav="presidents" class="nav-link" >Liste du PEL</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('presidents.create') }}" nav="presidents/create" class="nav-link" >Créer un Président</a>
                                </li>
                            </ul>
                        </div>
                    </li> <!-- end Dashboard Menu -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarOffice" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarOffice">
                            <i class="ri-team-fill"></i> <span >CDN</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarOffice">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('offices.index') }}" nav="offices" class="nav-link" >Liste du CDN</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('offices.create') }}" nav="offices/create" class="nav-link" >Créer un membre</a>
                                </li>
                            </ul>
                        </div>
                    </li> <!-- end Dashboard Menu -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarOpportunity" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarOpportunity">
                            <i class="mdi mdi-handshake-outline"></i> <span >Opportunités</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarOpportunity">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('opportunities.index') }}" nav="opportunities" class="nav-link" >Liste des opportunités</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('opportunities.create') }}" nav="opportunities/create" class="nav-link" >Créer une opportunité</a>
                                </li>
                            </ul>
                        </div>
                    </li> <!-- end Dashboard Menu -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarGallery" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarGallery">
                            <i class="ri-image-line"></i> <span >Galérie</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarGallery">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('galleries.index') }}" nav="galleries" class="nav-link" >Liste des images</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('galleries.create') }}" nav="galleries/create" class="nav-link" >Ajouter des images</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('galleries.categories.index') }}" nav="galleries/categories" class="nav-link" >Catégories</a>
                                </li>
                            </ul>
                        </div>
                    </li> <!-- end Dashboard Menu -->
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarLibrary" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarLibrary">
                            <i class="ri-image-line"></i> <span >Bibliothèque</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarLibrary">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('library.index') }}" nav="library" class="nav-link" >Liste des documents</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('library.create') }}" nav="library/create" class="nav-link" >Ajouter un document</a>
                                </li>
                            </ul>
                        </div>
                    </li> <!-- end Dashboard Menu -->
                @endif
                @if(Auth::user()->getIsAdminAttribute())
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarUser" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarUser">
                            <i class="ri-admin-line"></i> <span >Utilisateurs</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarUser">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('users.index', ['role' => 'membre']) }}" nav="users?role=membre" class="nav-link" >Liste des membres</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('users.index', ['role' => 'admin']) }}" nav="users?role=admin" class="nav-link" >Listes des administrateurs</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('users.create') }}" nav="users/create" class="nav-link" >Ajouter un  administrateur</a>
                                </li>
                            </ul>
                        </div>
                    </li> <!-- end Dashboard Menu -->
                @else
                    <li class="nav-item">
                        <a href="{{ route('my.profile') }}" nav="myprofile" class="nav-link menu-link">
                        <i class=" ri-admin-line"></i>Mon profil</a>
                    </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
