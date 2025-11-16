@extends('backend.layouts')
@section('title')
    Avis
@endsection
@section('content')
    <div class="px-3 px-lg-6 px-xxl-13 py-5 py-lg-10">
        <div class="mb-6">
            <h2 class="mb-0 text-heading fs-22 lh-15">Avis<span
                    class="badge badge-white badge-pill text-primary fs-18 font-weight-bold ml-2 avis-count">{{ $avis->total() }}</span>
            </h2>
            <p class="mb-1">Avis sur les services</p>
        </div>
        <div class="card border-0 mb-4">
            <div class="card-body p-0 p-sm-8 p-4">
                <h3 class="fs-16 lh-2 text-heading mb-0 d-inline-block pr-4 border-bottom border-primary">
                    <span class="avis-count">{{ $avis->count() }}</span> avis</h3>
                @forelse ($avis as $row)
                    <div class="media border-top pt-7 pb-6 d-sm-flex d-block text-sm-left text-center">
                        <img width="82"
                            src="{{ $row->user ? $row->user->photo_url : (new App\Models\User())->photo_url }}"
                            alt="{{ $row->nom }}" class="rounded-circle mr-sm-8 mb-4 mb-sm-0">
                        <div class="media-body">
                            <div class="row mb-1 align-items-center">
                                <div class="col-sm-6 mb-2 mb-sm-0">
                                    <h4 class="mb-0 text-heading fs-14">
                                        @if ($row->user)
                                            @can('view', $row->user)
                                                <a
                                                    href="{{ route('users.show', $row->user) }}">{{ $row->user->full_name }}</a>
                                            @else
                                                {{ $row->user->full_name }}
                                            @endcan
                                        @else
                                            {{ $row->nom }} ({{ $row->email }})
                                        @endif
                                    </h4>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="list-inline d-flex justify-content-sm-end justify-content-center mb-0">
                                        {!! str_repeat(
    '<li class="list-inline-item mr-1">
                                      <span class="text-warning fs-12 lh-2"><i class="fas fa-star"></i></span>
                                      </li>',
    $row->note,
) !!}
                                        {!! str_repeat(
    '<li class="list-inline-item mr-1">
                                      <span class="text-border fs-12 lh-2"><i class="fas fa-star"></i></span>
                                      </li>',
    5 - $row->note,
) !!}
                                    </ul>
                                </div>
                            </div>
                            <p class="mb-3 pr-xl-17">{!! nl2br(htmlspecialchars($row->comment)) !!}</p>
                            <div class="d-flex justify-content-sm-start justify-content-center">
                                <p class="mb-0 text-muted fs-13 lh-1">
                                    {{ Carbon::parse($row->created_at)->isoformat('DD MMM YYYY à H:mm') }}</p>
                                <a href="javascript:;" data-href="{{ route('avis.validate', $row) }}"
                                    data-actif="{{ $row->actif ? 'non' : 'oui' }}"
                                    class="publish-review mb-0 border-left border-dark @if ($row->actif) text-warning @else text-info @endif lh-1 ml-2 pl-2">@if ($row->actif) Dé-publier @else Publier @endif</a>
                              <a href="
                                    javascript:;" data-href="{{ route('avis.destroy', $row) }}"
                                    class="delete-review mb-0 border-left border-dark text-danger lh-1 ml-2 pl-2">Supprimer</a>
                                <a href="{{ route('public.services.show', $row->service) }}"
                                    class="mb-0 border-left border-dark lh-1 ml-2 pl-2">{{ $row->service->label }}</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="media border-top py-6 d-sm-flex d-block text-sm-left text-center">Il n'y a rien à afficher
                        pour le moment.</div>
                @endforelse

                <div class="mt-8 border-top pt-4">
                    {{ $avis->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('specific-js')
    <script>
        jQuery(function() {
            $('.publish-review').click(function(e) {
                e.preventDefault();
                let elt = $(this);
                $('#content').waitMe(waitMe_config);
                $.post(elt.data('href'), {
                        _token: $("meta[name=csrf-token]").attr("content"),
                        _method: 'PATCH',
                        actif: elt.data('actif')
                    })
                    .done((r) => {
                        $('#content').waitMe('hide');
                        elt.data('actif', r.avis.actif ? 'non' : 'oui')
                            .toggleClass('text-warning')
                            .toggleClass('text-info')
                            .text(r.avis.actif ? 'Dé-publier' : 'Publier');
                    })
                    .fail((er) => {
                        $('#content').waitMe('hide');
                        Swal.fire({
                            text: er.responseJSON.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "D'accord",
                            customClass: {
                                confirmButton: "btn fw-bold btn-danger",
                            },
                        });
                    });
            });


            $('.delete-review').click(function(e) {
                e.preventDefault();
                // Select parent row
                let parent = $(this).closest('.media');
                let elt = $(this);

                Swal.fire({
                    text: "Souhaitez-vous vraiment supprimer cet avis ?",
                    icon: "question",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Oui, je confirme",
                    cancelButtonText: "Non, annuler",
                    customClass: {
                        confirmButton: "btn font-weight-bold btn-danger",
                        cancelButton: "ml-3 btn font-weight-bold btn-active-light-primary",
                    },
                }).then(function(result) {
                    if (result.value) {
                        $('#content').waitMe(waitMe_config);
                        $.post(elt.data('href'), {
                                _token: $("meta[name=csrf-token]").attr("content"),
                                _method: 'DELETE',
                            })
                            .done((r) => {
                                $('#content').waitMe('hide');
                                parent.fadeOut('slow', function(el) {
                                    parent.remove()
                                    $('.avis-count').text(parseInt($('.avis-count')[0].innerText)-1);
                                })
                            })
                            .fail((er) => {
                                $('#content').waitMe('hide');
                                Swal.fire({
                                    text: er.responseJSON.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "D'accord",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-danger",
                                    },
                                });
                            });
                    }
                });
            });
        })
    </script>
@endsection
