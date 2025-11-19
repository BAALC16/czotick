@extends('organization.layouts.app')

@section('title', 'Attribuer des commissions')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Attribuer des commissions - {{ $event->event_title }}</h1>
        <a href="{{ route('org.events.show', ['org_slug' => $orgSlug, 'event' => $event->id]) }}" 
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('org.collaborateurs.store-commission', ['org_slug' => $orgSlug, 'eventId' => $event->id]) }}" 
                          method="POST" id="commissionForm">
                        @csrf

                        <div id="commissions-container">
                            @foreach($referrers as $referrer)
                                @php
                                    $existing = $existingCommissions->get($referrer->id);
                                @endphp
                                <div class="card mb-3 commission-item">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $referrer->name }} <small class="text-muted">({{ $referrer->referrer_code }})</small></h5>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Type de commission</label>
                                                <select name="commissions[{{ $referrer->id }}][commission_type]" 
                                                        class="form-select commission-type" 
                                                        data-referrer-id="{{ $referrer->id }}">
                                                    <option value="percentage" {{ ($existing && $existing->commission_type === 'percentage') ? 'selected' : '' }}>
                                                        Pourcentage
                                                    </option>
                                                    <option value="fixed" {{ ($existing && $existing->commission_type === 'fixed') ? 'selected' : '' }}>
                                                        Montant fixe
                                                    </option>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-4" id="percentage-field-{{ $referrer->id }}" 
                                                 style="display: {{ ($existing && $existing->commission_type === 'percentage') || !$existing ? 'block' : 'none' }};">
                                                <label class="form-label">Pourcentage (%)</label>
                                                <input type="number" 
                                                       name="commissions[{{ $referrer->id }}][commission_rate]" 
                                                       class="form-control" 
                                                       step="0.01" 
                                                       min="0" 
                                                       max="100"
                                                       value="{{ $existing ? $existing->commission_rate : '' }}"
                                                       {{ ($existing && $existing->commission_type === 'percentage') || !$existing ? 'required' : '' }}>
                                            </div>
                                            
                                            <div class="col-md-4" id="fixed-field-{{ $referrer->id }}" 
                                                 style="display: {{ ($existing && $existing->commission_type === 'fixed') ? 'block' : 'none' }};">
                                                <label class="form-label">Montant fixe (FCFA)</label>
                                                <input type="number" 
                                                       name="commissions[{{ $referrer->id }}][fixed_amount]" 
                                                       class="form-control" 
                                                       step="0.01" 
                                                       min="0"
                                                       value="{{ $existing ? $existing->fixed_amount : '' }}"
                                                       {{ ($existing && $existing->commission_type === 'fixed') ? 'required' : '' }}>
                                            </div>
                                        </div>

                                        <input type="hidden" name="commissions[{{ $referrer->id }}][referrer_id]" value="{{ $referrer->id }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">Enregistrer les commissions</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.commission-type').forEach(select => {
        select.addEventListener('change', function() {
            const referrerId = this.dataset.referrerId;
            const type = this.value;
            
            const percentageField = document.getElementById(`percentage-field-${referrerId}`);
            const fixedField = document.getElementById(`fixed-field-${referrerId}`);
            const percentageInput = percentageField.querySelector('input');
            const fixedInput = fixedField.querySelector('input');
            
            if (type === 'percentage') {
                percentageField.style.display = 'block';
                fixedField.style.display = 'none';
                percentageInput.setAttribute('required', 'required');
                fixedInput.removeAttribute('required');
                fixedInput.value = '';
            } else {
                percentageField.style.display = 'none';
                fixedField.style.display = 'block';
                fixedInput.setAttribute('required', 'required');
                percentageInput.removeAttribute('required');
                percentageInput.value = '';
            }
        });
    });
});
</script>
@endpush
@endsection

