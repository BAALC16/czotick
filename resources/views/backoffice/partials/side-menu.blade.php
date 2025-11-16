<li class="list-group-item pt-6 pb-4">
  <!--h5 class="fs-13 letter-spacing-087 text-muted mb-3 text-uppercase px-3">Manage Listings</h5-->
  <ul class="list-group list-group-no-border rounded-lg">
    <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="#services_collapse"
                 class="text-heading lh-1 sidebar-link d-flex align-items-center"
                 data-toggle="collapse" aria-haspopup="true" aria-expanded="false">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
          <i class="fas fa-cubes"></i>
        </span>
        <span class="sidebar-item-text">Services</span>
        <span class="d-inline-block ml-auto"><i class="fal fa-angle-down"></i></span>
      </a>
    </li>
    <div class="collapse" id="services_collapse">
      <div class="card card-body border-0 bg-transparent py-0 pl-6">
        <ul class="list-group list-group-flush list-group-no-border">
          @can('viewAny', App\Models\Service::class)
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link" href="{{ route('services.index') }}">Tous les services</a>
            </li>
          @else
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link" href="{{ route('public.services.index') }}">Services disponibles</a>
            </li>
          @endcan
          @can('create', App\Models\Service::class)            
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link"
              href="{{ route('services.create') }}">Ajouter un service</a>
            </li>
          @endcan
          @can('viewAny', App\Models\Reservation::class)
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link"
              href="{{route('reservations.index')}}">Demandes</a>
            </li>
          @endcan
        </ul>
      </div>
    </div>
    <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="#reservations_collapse"
                 class="text-heading lh-1 sidebar-link d-flex align-items-center"
                 data-toggle="collapse" aria-haspopup="true" aria-expanded="false">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
          <i class="fas fa-parachute-box"></i>
        </span>
        <span class="sidebar-item-text">Demandes de services</span>
        <span class="d-inline-block ml-auto"><i class="fal fa-angle-down"></i></span>
      </a>
    </li>
    <div class="collapse" id="reservations_collapse">
      <div class="card card-body border-0 bg-transparent py-0 pl-6">
        <ul class="list-group list-group-flush list-group-no-border">
          @can('viewAny', App\Models\Reservation::class)
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link"
              href="{{route('reservations.index')}}">Toutes les Demandes</a>
            </li>
          @endcan
          @can('viewAssigned', App\Models\Reservation::class)
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link" href="{{ route('reservations.list', ['filter' => 'assigned']) }}">Assignées à Moi</a>
            </li>
          @endcan
          @can('viewMine', App\Models\Reservation::class)            
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link"
              href="{{ route('reservations.list', ['filter' => 'mine']) }}">Mes Demandes</a>
            </li>
          @endcan
          <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
            <a class="text-heading lh-1 sidebar-link"
            href="{{ route('public.services.index') }}">Faire une Demande</a>
          </li>
          @can('viewAny', App\Models\Devis::class)
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link"
              href="{{route('devis.index', ['reservation' => 'all'])}}">Devis</a>
            </li>
          @else
            @can('viewCreated', App\Models\Devis::class)
              <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
                <a class="text-heading lh-1 sidebar-link"
                href="{{route('devis.index', ['reservation' => 'all', 'filter' => 'created'])}}">Devis créés</a>
              </li>
            @endcan
            @can('viewMine', App\Models\Devis::class)
              <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
                <a class="text-heading lh-1 sidebar-link"
                href="{{route('devis.index', ['reservation' => 'all', 'filter' => 'mine'])}}">Mes devis</a>
              </li>
            @endcan
          @endcan
        </ul>
      </div>
    </div>
    @can('viewAny', App\Models\User::class)
      <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
        <a href="#users_collapse"
                  class="text-heading lh-1 sidebar-link d-flex align-items-center"
                  data-toggle="collapse" aria-haspopup="true" aria-expanded="false">
          <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
            <i class="fas fa-users"></i>
          </span>
          <span class="sidebar-item-text">Utilisateurs</span>
          <span class="d-inline-block ml-auto"><i class="fal fa-angle-down"></i></span>
        </a>
      </li>
      <div class="collapse" id="users_collapse">
        <div class="card card-body border-0 bg-transparent py-0 pl-6">
          <ul class="list-group list-group-flush list-group-no-border">
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link" href="{{ route('users.index') }}">Tous les utilisateurs</a>
            </li>
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link" href="{{ route('users.index', ['role' => 'admin']) }}">Administrateurs</a>
            </li>
            <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
              <a class="text-heading lh-1 sidebar-link" href="{{ route('users.index', ['role' => 'prestataire']) }}">Prestataires</a>
            </li>
          </ul>
        </div>
      </div>      
    @endcan
    <!--li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="dashboard-add-new-property.html"
             class="text-heading lh-1 sidebar-link">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20 fs-20">
          <svg class="icon icon-add-new"><use
                          xlink:href="#icon-add-new"></use></svg></span>
        <span class="sidebar-item-text">Add new</span>
      </a>
    </li>
    <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="dashboard-my-properties.html"
             class="text-heading lh-1 sidebar-link d-flex align-items-center">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
          <svg class="icon icon-my-properties"><use
                          xlink:href="#icon-my-properties"></use></svg>
        </span>
        <span class="sidebar-item-text">My Properties</span>
        <span class="sidebar-item-number ml-auto text-primary fs-15 font-weight-bold">29</span>
      </a>
    </li>
    <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="dashboard-my-favorites.html"
             class="text-heading lh-1 sidebar-link d-flex align-items-center">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
          <svg class="icon icon-heart"><use xlink:href="#icon-heart"></use></svg>
        </span>
        <span class="sidebar-item-text">My Favorites</span>
        <span class="sidebar-item-number ml-auto text-primary fs-15 font-weight-bold">5</span>
      </a>
    </li>
    <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="dashboard-save-search.html"
             class="text-heading lh-1 sidebar-link d-flex align-items-center">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
          <svg class="icon icon-save-search"><use xlink:href="#icon-save-search"></use></svg>
        </span>
        <span class="sidebar-item-text">Save Search</span>
        <span class="sidebar-item-number ml-auto text-primary fs-15 font-weight-bold">5</span>
      </a>
    </li-->
    @can('viewAny', App\Models\Avis::class)
      <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
        <a href="{{route('avis.index')}}"
              class="text-heading lh-1 sidebar-link d-flex align-items-center">
          <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
            <svg class="icon icon-review"><use xlink:href="#icon-review"></use></svg>
          </span>
          <span class="sidebar-item-text">Avis</span>
          <span class="sidebar-item-number ml-auto text-primary fs-15 font-weight-bold">{{App\Models\Avis::count()}}</span>
        </a>
      </li>      
    @endcan
    <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="#invoice_collapse"
                 class="text-heading lh-1 sidebar-link d-flex align-items-center"
                 data-toggle="collapse" aria-haspopup="true" aria-expanded="false">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
          <i class="fal fa-file-invoice"></i>
        </span>
        <span class="sidebar-item-text">Invoice</span>
        <span class="d-inline-block ml-auto"><i class="fal fa-angle-down"></i></span>
      </a>
    </li>
  </ul>
  <div class="collapse" id="invoice_collapse">
    <div class="card card-body border-0 bg-transparent py-0 pl-6">
      <ul class="list-group list-group-flush list-group-no-border">
        <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
          <a class="text-heading lh-1 sidebar-link"
                     href="dashboard-invoice-listing.html">Listing Invoice</a>
        </li>
        <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
          <a class="text-heading lh-1 sidebar-link"
                     href="dashboard-add-new-invoice.html">Add New Invoice</a>
        </li>
        <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
          <a class="text-heading lh-1 sidebar-link" href="dashboard-edit-invoice.html">Edit
            Invoice</a>
        </li>
        <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
          <a class="text-heading lh-1 sidebar-link"
                     href="dashboard-preview-invoice.html">Preview Invoice</a>
        </li>
      </ul>
    </div>
  </div>
</li>
<li class="list-group-item pt-6 pb-4">
  <!--h5 class="fs-13 letter-spacing-087 text-muted mb-3 text-uppercase px-3">Compte</h5-->
  <ul class="list-group list-group-no-border rounded-lg">
    <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="#role_collapse" class="text-heading lh-1 sidebar-link d-flex align-items-center"
                 data-toggle="collapse" aria-haspopup="true" aria-expanded="false">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
          <i class="fal fa-file-invoice"></i>
        </span>
        <span class="sidebar-item-text">R&ocirc;les</span>
        <span class="d-inline-block ml-auto"><i class="fal fa-angle-down"></i></span>
      </a>
    </li>
  <div class="collapse" id="role_collapse">
    <div class="card card-body border-0 bg-transparent py-0 pl-6">
      <ul class="list-group list-group-flush list-group-no-border">
    @can('viewAny', App\Models\Role::class)
        <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
          <a class="text-heading lh-1 sidebar-link" href="{{ route('roles.index') }}">Gestion des R&ocirc;les</a>
        </li>
    @endcan
        <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
          <a class="text-heading lh-1 sidebar-link" href="{{ route('my.roles') }}">Mes R&ocirc;les</a>
        </li>
    @can('viewAny', App\Models\Role::class)
        <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
          <a class="text-heading lh-1 sidebar-link" href="{{ route('permissions.index') }}">Permissions</a>
        </li>
    @endcan
      </ul>
    </div>
  </div>
    <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="{{route('my.profile')}}"
             class="text-heading lh-1 sidebar-link">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
          <svg class="icon icon-my-profile"><use xlink:href="#icon-my-profile"></use></svg>
        </span>
        <span class="sidebar-item-text">Mon Profil</span>
      </a>
    </li>
    <li class="list-group-item px-3 px-xl-4 py-2 sidebar-item">
      <a href="{{route('logout')}}" class="text-heading lh-1 sidebar-link">
        <span class="sidebar-item-icon d-inline-block mr-3 text-muted fs-20">
          <svg class="icon icon-log-out"><use xlink:href="#icon-log-out"></use></svg>
        </span>
        <span class="sidebar-item-text">Déconnexion</span>
      </a>
    </li>
  </ul>
</li>
