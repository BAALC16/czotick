@extends('layouts.master')
@section('title') Liste des inscriptions @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Conventions @endslot
@slot('title') Liste des inscriptions @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des inscriptions</h5>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3 mb-2">
                    <div class="col-xxl-5 col-sm-6">
                        <div class="search-box">
                            <input type="text" id="searchInput" class="form-control search" placeholder="Rechercher à partir du numéro...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist"></ul>
                    <div class="table-responsive table-card mb-1">
                        <table class="table table-nowrap align-middle" id="orderTable">
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th scope="col" style="width: 25px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort">Nom & Prénoms</th>
                                    <th class="sort">Téléphone</th>
                                    <th class="sort">Email</th>
                                    <th class="sort">OLM/OLP/OLC</th>
                                    <th class="sort">Qualité</th>
                                    <th class="sort">Status</th>
                                    <th class="sort">Date</th>
                                    <th class="sort">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all" id="conventionList">
                                @forelse ($conventions as $convention)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input check-item" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td class="title">
                                        @can('view', $convention)
                                        <a class="text-dark hover-primary">{{ $convention->fullname }}</a>
                                        @else
                                        {{ $convention->fullname }}
                                        @endcan
                                    </td>
                                    <td class="phone">{{ $convention->phone }}</td>
                                    <td>{{ $convention->email }}</td>
                                    <td>{{ $convention->organization }} @if ($convention->other_organisation && $convention->organization == "Autre") {{ - $convention->other_organisation}} @endif</td>
                                    <td>{{ $convention->quality }}</td>
                                    <td>
                                        @if ($convention->paymentStatus == 1)
                                        <span class="badge badge-soft-success">Ticket payé</span>
                                        @else
                                        <span class="badge badge-soft-danger">Ticket non payé</span>
                                        @endif
                                    </td>
                                    <td class="date">{{ Carbon::parse($convention->created_at)->isoFormat("DD MMM YYYY") }}</td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                            @if ($convention->paymentStatus == 1)
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ticket">
                                                <a href="{{ route('download.ticket', $convention->id) }}" class="text-primary d-inline-block">
                                                    <i class="ri-file-download-fill fs-16"></i>
                                                </a>
                                            </li>
                                            @endif
                                            @if ($convention->paymentStatus == 0)
                                            <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Valider paiement">
                                                <a href="{{ route('conventions.validPayment', $convention) }}" class="text-primary d-inline-block edit-item-btn">
                                                    <i class="ri-check-fill fs-16"></i>
                                                </a>
                                            </li>
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="page-header-notifications-dropdown"></span>
    <span class="social-button"></span>
    <!--end col-->
</div>
<!--end row-->
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>

    <script src="{{ URL::asset('assets/libs/list.pagination.js/list.pagination.js.min.js') }}"></script>
    <!--ecommerce-customer init js -->
    <script src="{{ URL::asset('assets/js/pages/ecommerce-order.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de List.js
            var options = {
                valueNames: ['title', 'phone'],  // Liste des classes des éléments à filtrer
                item: '<tr><td class="title"></td><td class="phone"></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>'  // Structure des éléments
            };

            var userList = new List('orderList', options);

            // Gestion de la recherche
            document.getElementById('searchInput').addEventListener('keyup', function() {
                userList.search(this.value);
            });
        });
    </script>
@endsection
