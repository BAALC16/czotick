{{-- resources/views/components/dynamic-field.blade.php --}}
@php
    // NORMALISATION SÛRE pour gérer les deux formats
    $fieldConfigRaw = $field->field_config;
    
    if (is_string($fieldConfigRaw)) {
        // Si c'est une chaîne JSON, la décoder
        $fieldConfig = json_decode($fieldConfigRaw, true) ?? [];
    } elseif (is_array($fieldConfigRaw)) {
        // Si c'est déjà un tableau, l'utiliser tel quel
        $fieldConfig = $fieldConfigRaw;
    } else {
        // Sinon, tableau vide par défaut
        $fieldConfig = [];
    }
    
    $fieldName = $field->field_key;
    $fieldId = 'field_' . $field->field_key;
    $isRequired = $field->is_required;
    $fieldWidth = $field->field_width;
@endphp

<div class="form-group field-{{ $fieldWidth }}" data-field-key="{{ $fieldName }}">
    <label for="{{ $fieldId }}" class="{{ $isRequired ? 'required-label' : '' }}">
        {{ $field->field_label }}
    </label>
    
    @if($field->field_description)
        <p class="field-description">{{ $field->field_description }}</p>
    @endif
    
    @switch($field->field_type)
        @case('text')
        @case('email')
        @case('url')
        @case('password')
            <input type="{{ $field->field_type }}" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}" 
                   value="{{ old($fieldName) }}"
                   {{ $isRequired ? 'required' : '' }}
                   {{ $field->is_readonly ? 'readonly' : '' }}
                   @if(isset($fieldConfig['placeholder'])) placeholder="{{ $fieldConfig['placeholder'] }}" @endif
                   @if(isset($fieldConfig['pattern'])) pattern="{{ $fieldConfig['pattern'] }}" @endif
                   @if(isset($fieldConfig['min_length'])) minlength="{{ $fieldConfig['min_length'] }}" @endif
                   @if(isset($fieldConfig['max_length'])) maxlength="{{ $fieldConfig['max_length'] }}" @endif
            >
            @break

        @case('phone')
        @case('country_phone')
            @php
                // GESTION SÛRE des deux formats pour country_phone
                $showCountrySelector = $fieldConfig['show_country_selector'] ?? false;
                $countries = [];
                $defaultCountry = '+225'; // Valeur par défaut
                
                // Format complexe (JCI Abidjan Ivoire)
                if (isset($fieldConfig['countries']) && is_array($fieldConfig['countries'])) {
                    $countries = $fieldConfig['countries'];
                    $defaultCountry = $fieldConfig['default_country'] ?? '+225';
                    $showCountrySelector = count($countries) > 1;
                }
                // Format simple (INF JCI-CI)
                elseif (isset($fieldConfig['country_code'])) {
                    $defaultCountry = $fieldConfig['country_code'];
                    $countries = [['code' => $defaultCountry, 'name' => 'Côte d\'Ivoire', 'flag' => '🇨🇮']];
                    $showCountrySelector = false;
                }
                // Fallback par défaut
                else {
                    $countries = [['code' => '+225', 'name' => 'Côte d\'Ivoire', 'flag' => '🇨🇮']];
                    $showCountrySelector = false;
                }
                
                // Récupérer la valeur sélectionnée précédemment
                $selectedCountry = old($fieldName . '_country', $defaultCountry);
                $phoneValue = old($fieldName, '');
            @endphp
            
            @if($showCountrySelector && count($countries) > 1)
                <div style="display: flex; gap: 0.5rem;">
                    <select name="{{ $fieldName }}_country" 
                            id="{{ $fieldId }}_country"
                            style="flex: 0 0 120px;"
                            onchange="updatePhoneCountry('{{ $fieldId }}')">
                        @foreach($countries as $country)
                            <option value="{{ $country['code'] ?? '' }}" 
                                    {{ ($country['code'] ?? '') === $selectedCountry ? 'selected' : '' }}>
                                {{ $country['flag'] ?? '' }} {{ $country['code'] ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    <input type="tel" 
                            id="{{ $fieldId }}" 
                            name="{{ $fieldName }}" 
                            value="{{ $phoneValue }}"
                            style="flex: 1;"
                            {{ $isRequired ? 'required' : '' }}
                            data-country-field="{{ $fieldId }}_country"
                            @if(isset($fieldConfig['placeholder'])) placeholder="{{ $fieldConfig['placeholder'] }}" @endif
                            @if(isset($fieldConfig['pattern'])) pattern="{{ $fieldConfig['pattern'] }}" @endif
                            @if(isset($fieldConfig['min_length'])) minlength="{{ $fieldConfig['min_length'] }}" @endif
                            @if(isset($fieldConfig['max_length'])) maxlength="{{ $fieldConfig['max_length'] }}" @endif
                    >
                </div>
            @else
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    @if(!empty($defaultCountry))
                        <span style="padding: 0.8rem; background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; color: #666;">
                            {{ $defaultCountry }}
                        </span>
                        {{-- Champ caché pour stocker le code pays --}}
                        <input type="hidden" name="{{ $fieldName }}_country" value="{{ $defaultCountry }}">
                    @endif
                    <input type="tel" 
                            id="{{ $fieldId }}" 
                            name="{{ $fieldName }}" 
                            value="{{ $phoneValue }}"
                            style="flex: 1;"
                            {{ $isRequired ? 'required' : '' }}
                            @if(isset($fieldConfig['placeholder'])) placeholder="{{ $fieldConfig['placeholder'] }}" @endif
                            @if(isset($fieldConfig['pattern'])) pattern="{{ $fieldConfig['pattern'] }}" @endif
                            @if(isset($fieldConfig['min_length'])) minlength="{{ $fieldConfig['min_length'] }}" @endif
                            @if(isset($fieldConfig['max_length'])) maxlength="{{ $fieldConfig['max_length'] }}" @endif
                    >
                </div>
            @endif
            @break
        
        @case('number')
            <input type="number" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}" 
                   value="{{ old($fieldName) }}"
                   {{ $isRequired ? 'required' : '' }}
                   {{ $field->is_readonly ? 'readonly' : '' }}
                   @if(isset($fieldConfig['placeholder'])) placeholder="{{ $fieldConfig['placeholder'] }}" @endif
                   @if(isset($fieldConfig['min'])) min="{{ $fieldConfig['min'] }}" @endif
                   @if(isset($fieldConfig['max'])) max="{{ $fieldConfig['max'] }}" @endif
                   @if(isset($fieldConfig['step'])) step="{{ $fieldConfig['step'] }}" @endif
            >
            @break
            
        @case('date')
            <input type="date" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}" 
                   value="{{ old($fieldName) }}"
                   {{ $isRequired ? 'required' : '' }}
                   {{ $field->is_readonly ? 'readonly' : '' }}
                   @if(isset($fieldConfig['min_date'])) min="{{ $fieldConfig['min_date'] }}" @endif
                   @if(isset($fieldConfig['max_date'])) max="{{ $fieldConfig['max_date'] }}" @endif
            >
            @break
            
        @case('time')
            <input type="time" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}" 
                   value="{{ old($fieldName) }}"
                   {{ $isRequired ? 'required' : '' }}
                   {{ $field->is_readonly ? 'readonly' : '' }}
            >
            @break
            
        @case('datetime')
            <input type="datetime-local" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}" 
                   value="{{ old($fieldName) }}"
                   {{ $isRequired ? 'required' : '' }}
                   {{ $field->is_readonly ? 'readonly' : '' }}
            >
            @break
            
        @case('textarea')
            <textarea id="{{ $fieldId }}" 
                      name="{{ $fieldName }}" 
                      {{ $isRequired ? 'required' : '' }}
                      {{ $field->is_readonly ? 'readonly' : '' }}
                      @if(isset($fieldConfig['placeholder'])) placeholder="{{ $fieldConfig['placeholder'] }}" @endif
                      @if(isset($fieldConfig['rows'])) rows="{{ $fieldConfig['rows'] }}" @endif
                      @if(isset($fieldConfig['max_length'])) maxlength="{{ $fieldConfig['max_length'] }}" @endif
            >{{ old($fieldName) }}</textarea>
            @break
            
        @case('select')
            @php
                $options = $fieldConfig['options'] ?? [];
                $allowOther = $fieldConfig['allow_other'] ?? false;
                
                // S'assurer que $options est un tableau
                if (!is_array($options)) {
                    $options = [];
                }
            @endphp
            
            <select id="{{ $fieldId }}" 
                    name="{{ $fieldName }}" 
                    {{ $isRequired ? 'required' : '' }}
                    {{ $field->is_readonly ? 'disabled' : '' }}>
                @if(isset($fieldConfig['placeholder']))
                    <option value="">{{ $fieldConfig['placeholder'] }}</option>
                @endif
                
                @foreach($options as $option)
                    @php
                        // Gérer les deux formats d'options : string ou array
                        if (is_string($option)) {
                            $optionValue = $option;
                            $optionLabel = $option;
                        } else {
                            $optionValue = $option['value'] ?? '';
                            $optionLabel = $option['label'] ?? $optionValue;
                        }
                    @endphp
                    <option value="{{ $optionValue }}" {{ old($fieldName) === $optionValue ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
                
                @if($allowOther)
                    <option value="other" {{ old($fieldName) === 'other' ? 'selected' : '' }}>
                        Autre
                    </option>
                @endif
            </select>
            
            @if($allowOther)
                <div class="other-field {{ old($fieldName) === 'other' ? 'show' : '' }}">
                    <input type="text" 
                           name="{{ $fieldName }}_other" 
                           placeholder="{{ $fieldConfig['other_placeholder'] ?? 'Précisez...' }}"
                           value="{{ old($fieldName . '_other') }}"
                           {{ old($fieldName) === 'other' ? 'required' : '' }}>
                </div>
            @endif
            @break
            
        @case('radio')
            @php
                $options = $fieldConfig['options'] ?? [];
                $layout = $fieldConfig['layout'] ?? 'vertical';
                
                // S'assurer que $options est un tableau
                if (!is_array($options)) {
                    $options = [];
                }
            @endphp
            
            <div class="radio-group {{ $layout }}">
                @foreach($options as $index => $option)
                    @php
                        // Gérer les deux formats d'options
                        if (is_string($option)) {
                            $optionValue = $option;
                            $optionLabel = $option;
                        } else {
                            $optionValue = $option['value'] ?? '';
                            $optionLabel = $option['label'] ?? $optionValue;
                        }
                    @endphp
                    <div class="radio-option {{ old($fieldName) === $optionValue ? 'selected' : '' }}" 
                         onclick="selectRadioOption(this, '{{ $fieldId }}_{{ $index }}')">
                        <input type="radio" 
                               id="{{ $fieldId }}_{{ $index }}" 
                               name="{{ $fieldName }}" 
                               value="{{ $optionValue }}"
                               {{ $isRequired ? 'required' : '' }}
                               {{ old($fieldName) === $optionValue ? 'checked' : '' }}>
                        <label for="{{ $fieldId }}_{{ $index }}">{{ $optionLabel }}</label>
                    </div>
                @endforeach
            </div>
            @break
            
        @case('checkbox')
            <div class="checkbox-option" onclick="toggleCheckbox('{{ $fieldId }}')">
                <input type="checkbox" 
                       id="{{ $fieldId }}" 
                       name="{{ $fieldName }}" 
                       value="1"
                       {{ old($fieldName) ? 'checked' : '' }}>
                <label for="{{ $fieldId }}">{{ $fieldConfig['label'] ?? 'Oui' }}</label>
            </div>
            @break
            
        @case('checkbox_group')
            @php
                $options = $fieldConfig['options'] ?? [];
                $minSelections = $fieldConfig['min_selections'] ?? 0;
                $maxSelections = $fieldConfig['max_selections'] ?? null;
                $oldValues = old($fieldName, []);
                if (!is_array($oldValues)) {
                    $oldValues = [];
                }
                
                // S'assurer que $options est un tableau
                if (!is_array($options)) {
                    $options = [];
                }
            @endphp
            
            <div class="checkbox-group" 
                 data-min-selections="{{ $minSelections }}" 
                 data-max-selections="{{ $maxSelections }}"
                 data-field-key="{{ $fieldName }}">
                @foreach($options as $index => $option)
                    @php
                        // Gérer les deux formats d'options
                        if (is_string($option)) {
                            $optionValue = $option;
                            $optionLabel = $option;
                        } else {
                            $optionValue = $option['value'] ?? '';
                            $optionLabel = $option['label'] ?? $optionValue;
                        }
                    @endphp
                    <div class="checkbox-option {{ in_array($optionValue, $oldValues) ? 'selected' : '' }}" 
                         onclick="selectCheckboxWithOther(this, '{{ $fieldId }}_{{ $index }}', '{{ $fieldName }}')">
                        <input type="checkbox" 
                               id="{{ $fieldId }}_{{ $index }}" 
                               name="{{ $fieldName }}[]" 
                               value="{{ $optionValue }}"
                               {{ in_array($optionValue, $oldValues) ? 'checked' : '' }}>
                        <label for="{{ $fieldId }}_{{ $index }}">{{ $optionLabel }}</label>
                    </div>
                @endforeach
            </div>
            @break
            
        @case('file')
            @php
                $acceptedTypes = $fieldConfig['accepted_types'] ?? [];
                $maxSizeMb = $fieldConfig['max_size_mb'] ?? 5;
                $multiple = $fieldConfig['multiple'] ?? false;
            @endphp
            
            <input type="file" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}{{ $multiple ? '[]' : '' }}" 
                   {{ $isRequired ? 'required' : '' }}
                   {{ $multiple ? 'multiple' : '' }}
                   @if(!empty($acceptedTypes) && is_array($acceptedTypes)) accept="{{ implode(',', $acceptedTypes) }}" @endif
                   data-max-size="{{ $maxSizeMb }}"
            >
            
            @if(isset($fieldConfig['help_text']))
                <small class="field-help-text">{{ $fieldConfig['help_text'] }}</small>
            @endif
            @break
            
        @default
            <input type="text" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}" 
                   value="{{ old($fieldName) }}"
                   {{ $isRequired ? 'required' : '' }}
                   @if(isset($fieldConfig['placeholder'])) placeholder="{{ $fieldConfig['placeholder'] }}" @endif
            >
    @endswitch
    
    @if($field->field_help_text)
        <div class="field-help-text">{{ $field->field_help_text }}</div>
    @endif
</div>

<script>
function selectRadioOption(element, radioId) {
    // Supprimer selected de tous les radios du même groupe
    const radioGroup = element.closest('.radio-group');
    radioGroup.querySelectorAll('.radio-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Ajouter selected à l'option cliquée
    element.classList.add('selected');
    
    // Sélectionner le radio
    document.getElementById(radioId).checked = true;
    
    // Déclencher l'événement change pour gérer les options "Autre"
    document.getElementById(radioId).dispatchEvent(new Event('change'));
}

function toggleCheckbox(checkboxId) {
    const checkbox = document.getElementById(checkboxId);
    const option = checkbox.closest('.checkbox-option');
    
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        option.classList.add('selected');
    } else {
        option.classList.remove('selected');
    }
}

// NOUVELLE FONCTION pour gérer les checkbox avec option "Autre"
function selectCheckboxWithOther(element, checkboxId, fieldName) {
    const checkbox = document.getElementById(checkboxId);
    const group = element.closest('.checkbox-group');
    const maxSelections = parseInt(group.dataset.maxSelections);
    
    // Compter les cases cochées actuellement
    const checkedBoxes = group.querySelectorAll('input[type="checkbox"]:checked');
    
    // Vérifier la limite AVANT de cocher
    if (!checkbox.checked && maxSelections && checkedBoxes.length >= maxSelections) {
        // Afficher une erreur et empêcher la sélection
        const fieldGroup = group.closest('.form-group');
        showFieldError(fieldGroup.querySelector('input'), 
            `Vous ne pouvez sélectionner que ${maxSelections} option(s) maximum.`);
        
        setTimeout(() => {
            clearFieldError(fieldGroup.querySelector('input'));
        }, 3000);
        
        return; // Empêcher la sélection
    }
    
    // Inverser l'état de la checkbox
    checkbox.checked = !checkbox.checked;
    
    // Mettre à jour l'apparence visuelle
    if (checkbox.checked) {
        element.classList.add('selected');
    } else {
        element.classList.remove('selected');
    }
    
    // GESTION SPÉCIALE pour l'option "Autre"
    if (checkbox.value.toLowerCase() === 'autre') {
        console.log('🔧 Gestion de l\'option "Autre" détectée');
        
        // Déclencher l'événement change pour le système principal
        const changeEvent = new Event('change', { bubbles: true });
        checkbox.dispatchEvent(changeEvent);
    }
}
</script>