@extends('layouts.master')
@section('title') Liste des Demandes d'information @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Demandes d'information @endslot
@slot('title') Toutes les Demandes d'information @endslot
@endcomponent
<div class="row">
    <div class="col-lg-12">
        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des Demandes</h5>
                </div>
            </div>
            <div class="card-body pt-0">
                <div>
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                    </ul>

                    <div class="table-responsive table-card mb-1">
                        <table class="table table-nowrap align-middle" id="orderTable">
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th scope="col" style="width: 25px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="date">Date</th>
                                    <th class="sort" data-sort="details">D&eacute;tails</th>
                                    <th class="sort" data-sort="fullname">Nom</th>
                                    <th class="sort" data-sort="email">Email</th>
                                    <th class="sort" data-sort="phone">Phone</th>
                                    <th class="sort" data-sort="status">Statut</th>
                                    <th class="sort" data-sort="options">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                            @forelse ($contacts as $contact)
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td class="date">{{ Carbon::createFromFormat('Y-m-d H:i:s', $contact->created_at)->format('d-m-Y H:i:s') }}</td>
                                    <td class="product_name">
                                        {{ $contact->subject }}
                                    </td>
                                    <td>{{$contact->fullname}}</td>
                                    <td>{{$contact->email}}</td>
                                    <td>{{$contact->phone}}</td>
                                    <td>
                                        @if ($contact->read_at)
                                        <span class="badge badge-soft-success text-uppercase">R&eacute;pondu</span>
                                        @else
                                        <span class="badge badge-soft-danger text-uppercase">Non Lu</span>
                                        @endif
                                    </td>
                                    <td>
                                        <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="D&eacute;tails">
                                            <a href="#" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#reply_{{$contact->id}}" >
                                                <i class="ri-eye-fill fs-16"></i>
                                            </a>
                                        </li>
                                        <!-- Varying modal content -->
                                        <div class="modal fade" id="reply_{{$contact->id}}" tabindex="-1" aria-labelledby="replyLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('contacts.store') }}" class="form-xhr blockui">
                                                    @csrf
                                                    <input type="hidden" name="_method" value="post" />
                                                    <input type="hidden" name="contact_id" value="{{$contact->id}}">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="varyingcontentModalLabel">R&eacute;pondre</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="recipient-name" class="col-form-label">Recipient:</label>
                                                                <input type="text" class="form-control" readonly id="recipient-name" value="{{$contact->fullname}}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="recipient-email" class="col-form-label">Email:</label>
                                                                <input type="text" class="form-control" readonly id="recipient-email" value="{{$contact->email}}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="message-text" class="col-form-label">Message:</label>
                                                                <textarea class="form-control" id="message-text">{{$contact->message}}</textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="message-reply" class="col-form-label">R&eacute;ponse:</label>
                                                                <textarea class="form-control" id="message-reply" name="reply"></textarea>
                                                            </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                                        <button type="submit" class="btn btn-primary">Envoyer message</button>
                                                    </div>
                                                    </form>

                                                </div>
                                            </div>
                                          </div>
                                    </td>

                                </tr>
                            @empty
                              <tr>
                                <td colspan="5" class="align-middle">Il n'y a rien à afficher pour le moment.</td>
                              </tr>
                            @endforelse

                            </tbody>
                        </table>
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                    trigger="loop" colors="primary:#405189,secondary:#0ab39c"
                                    style="width:75px;height:75px">
                                </lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                <p class="text-muted">We've searched more than 150+ Orders We did
                                    not find any
                                    orders for you search.</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="pagination-wrap hstack gap-2">
                            <a class="page-item pagination-prev disabled" href="#">
                                <<
                            </a>
                            <ul class="pagination listjs-pagination mb-0"></ul>
                            <a class="page-item pagination-next" href="#">
                                >>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body p-5 text-center">
                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                    trigger="loop" colors="primary:#405189,secondary:#f06548"
                                    style="width:90px;height:90px"></lord-icon>
                                <div class="mt-4 text-center">
                                    <h4>Vous &ecirc;tes sur le point de supprimer une promotion?</h4>
                                    <p class="text-muted fs-15 mb-4">Toutes les infos seront supprim&eacute;es de la Base de Donn&eacute;es.</p>
                                    <div class="hstack gap-2 justify-content-center remove">
                                        <button
                                            class="btn btn-link link-success fw-medium text-decoration-none"
                                            data-bs-dismiss="modal"><i
                                                class="ri-close-line me-1 align-middle"></i>
                                            Fermer</button>
                                        <button class="btn btn-danger" id="delete-record">Oui,
                                            Supprimer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end modal -->
            </div>
        </div>

    </div>
    <!--end col-->
</div>
<!--end row-->
@endsection
@section('script')
<script src="{{ URL::asset('assets/libs/list.js/list.js.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/list.pagination.js/list.pagination.js.min.js') }}"></script>
<!--ecommerce-customer init js -->
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
