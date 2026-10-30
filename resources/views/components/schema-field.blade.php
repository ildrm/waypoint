@php
    $schema = $schema ?? [];
    $fieldName = $fieldName ?? 'value';
    $fieldKey = $fieldKey ?? 'value';
    $fieldLabel = $schema['title'] ?? $fieldLabel ?? __('Your answer');
    $fieldId = 'field-'.preg_replace('/[^a-zA-Z0-9_-]/','-', $fieldName);
    $fieldValue = old($fieldKey, $fieldValue ?? null);
    $types = (array) ($schema['type'] ?? []);
    $fieldType = collect($types)->first(fn($type) => $type !== 'null') ?? 'string';
    $enumValues = $schema['enum'] ?? null;
    $fieldDescription = $schema['description'] ?? '';
    $hasError = $errors->has($fieldKey);
    $translatedFieldLabel = __($fieldLabel);
    $translatedFieldLabel = is_string($translatedFieldLabel) ? $translatedFieldLabel : $fieldLabel;
    $translatedFieldDescription = __($fieldDescription);
    $translatedFieldDescription = is_string($translatedFieldDescription) ? $translatedFieldDescription : $fieldDescription;
@endphp
@if($fieldType === 'object')
    <fieldset class="record-fields"><legend dir="auto" class="sr-only">{{ $translatedFieldLabel }}</legend>
        @foreach($schema['properties'] ?? [] as $property=>$propertySchema)
            @include('components.schema-field', ['schema'=>$propertySchema,'fieldName'=>$fieldName.'['.$property.']','fieldKey'=>$fieldKey.'.'.$property,'fieldLabel'=>\Illuminate\Support\Str::headline($property),'fieldValue'=>is_array($fieldValue) ? ($fieldValue[$property] ?? null) : null])
        @endforeach
    </fieldset>
@elseif(array_key_exists('const',$schema))
    <div class="field-group">
        <label dir="auto" for="{{ $fieldId }}">{{ $translatedFieldLabel }}</label>
        <p dir="auto" class="field-help" id="{{ $fieldId }}-help">{{ $translatedFieldDescription }} {{ __('This measurement definition is fixed, not a selected preference.') }}</p>
        <input id="{{ $fieldId }}" name="{{ $fieldName }}" type="text" dir="auto" value="{{ $schema['const'] }}" readonly aria-describedby="{{ $fieldId }}-help">
    </div>
@elseif($fieldType === 'boolean')
    <fieldset class="field-group boolean-field" aria-describedby="{{ $fieldId }}-help {{ $fieldId }}-error">
        <legend dir="auto">{{ $translatedFieldLabel }}</legend>
        <p dir="auto" class="field-help" id="{{ $fieldId }}-help">{{ $translatedFieldDescription }}</p>
        <div class="boolean-options">
            <label class="choice" for="{{ $fieldId }}-yes"><input type="radio" id="{{ $fieldId }}-yes" name="{{ $fieldName }}" value="1" @checked($fieldValue===true||$fieldValue===1||$fieldValue==='1') @if($hasError) aria-invalid="true" @endif><span dir="auto">{{ __('Yes') }}</span></label>
            <label class="choice" for="{{ $fieldId }}-no"><input type="radio" id="{{ $fieldId }}-no" name="{{ $fieldName }}" value="0" @checked($fieldValue===false||$fieldValue===0||$fieldValue==='0') @if($hasError) aria-invalid="true" @endif><span dir="auto">{{ __('No') }}</span></label>
            <label class="choice" for="{{ $fieldId }}-unknown"><input type="radio" id="{{ $fieldId }}-unknown" name="{{ $fieldName }}" value="" @checked($fieldValue==='') @if($hasError) aria-invalid="true" @endif><span dir="auto">{{ __('Not known / not decided') }}</span></label>
        </div>
        <span dir="auto" class="field-error" id="{{ $fieldId }}-error">@error($fieldKey)
@php
$localizedMessage=__($message);
@endphp
{{ is_string($localizedMessage)?$localizedMessage:$message }}@enderror</span>
    </fieldset>
@elseif(is_array($enumValues))
    <div class="field-group">
        <label dir="auto" for="{{ $fieldId }}">{{ $translatedFieldLabel }}</label>
        <p dir="auto" class="field-help" id="{{ $fieldId }}-help">{{ $translatedFieldDescription }}</p>
        <select id="{{ $fieldId }}" name="{{ $fieldName }}" aria-describedby="{{ $fieldId }}-help {{ $fieldId }}-error" @if($hasError) aria-invalid="true" @endif>
            <option value="">{{ __('Choose an answer') }}</option>
            @foreach($enumValues as $enumValue)
                @if($enumValue!==null)<option value="{{ is_bool($enumValue) ? ($enumValue?'1':'0') : $enumValue }}" @selected($fieldValue!==null && (string)$fieldValue===(string)$enumValue)>{{ ($schema['x-option-labels'][(string)$enumValue] ?? $schema['x-enum-labels'][(string)$enumValue] ?? \Illuminate\Support\Str::headline((string)$enumValue)) }}</option>@endif
            @endforeach
        </select>
        <span dir="auto" class="field-error" id="{{ $fieldId }}-error">@error($fieldKey)
@php
$localizedMessage=__($message);
@endphp
{{ is_string($localizedMessage)?$localizedMessage:$message }}@enderror</span>
    </div>
@elseif($fieldType === 'array' && ($question['id'] ?? '')==='Q-SW-AVAILABILITY-TEAM' && $fieldKey==='value.capacity')
    @include('components.capacity-field')
@elseif($fieldType === 'array')
    <div class="field-group">
        <label dir="auto" for="{{ $fieldId }}">{{ $translatedFieldLabel }}</label>
        <p dir="auto" class="field-help" id="{{ $fieldId }}-help">{{ $translatedFieldDescription }} {{ __('Enter one item per line.') }}@if(isset($schema['maxItems'])) {{ __('Maximum :count items.', ['count'=>$schema['maxItems']]) }}@endif</p>
        <textarea dir="auto" id="{{ $fieldId }}" name="{{ $fieldName }}" rows="4" data-list-input aria-describedby="{{ $fieldId }}-help {{ $fieldId }}-error" @if($hasError) aria-invalid="true" @endif>{{ is_array($fieldValue) ? implode("
", array_map(fn($item)=>is_scalar($item)?(string)$item:json_encode($item, JSON_UNESCAPED_UNICODE),$fieldValue)) : $fieldValue }}</textarea>
        <span dir="auto" class="field-error" id="{{ $fieldId }}-error">@error($fieldKey)
@php
$localizedMessage=__($message);
@endphp
{{ is_string($localizedMessage)?$localizedMessage:$message }}@enderror</span>
    </div>
@else
    
@php
        $numeric = in_array($fieldType,['integer','number']);
        $decimalString = !$numeric && ((isset($schema['pattern']) && str_contains($schema['pattern'],'[0-9]')) || preg_match('/(?:^|\.)(?:initial_min|initial_max|recurring_monthly_cap|amount|unit_amount|rate)$/i',$fieldKey));
        $dateInput = ($schema['format'] ?? '') === 'date' || preg_match('/(?:^|\.)(?:mandatory_date|preferred_date|start_date)$/i',$fieldKey);
        $longText = !$numeric && !$decimalString && !$dateInput && ($schema['maxLength'] ?? 0) > 500;

@endphp
    <div class="field-group">
        <label dir="auto" for="{{ $fieldId }}">{{ $translatedFieldLabel }}</label>
        <p dir="auto" class="field-help" id="{{ $fieldId }}-help">{{ $translatedFieldDescription }}@if($schema['x-null-meaning'] ?? false) {{ str_contains($fieldKey,'mandatory') ? __('Leave blank if there is no mandatory date.') : __('Leave blank if there is no preferred date.') }}@endif</p>
        @if($longText)
            <textarea dir="auto" id="{{ $fieldId }}" name="{{ $fieldName }}" rows="3" maxlength="{{ $schema['maxLength'] ?? 2000 }}" aria-describedby="{{ $fieldId }}-help {{ $fieldId }}-error" @if($hasError) aria-invalid="true" @endif>{{ is_scalar($fieldValue)?$fieldValue:'' }}</textarea>
        @else
            <input id="{{ $fieldId }}" name="{{ $fieldName }}" type="{{ $numeric?'number':($dateInput?'date':'text') }}" dir="{{ ($numeric || $decimalString || $dateInput)?'ltr':'auto' }}" value="{{ is_scalar($fieldValue)?$fieldValue:'' }}" @if($numeric) inputmode="{{ $fieldType==='integer'?'numeric':'decimal' }}" step="{{ $fieldType==='integer'?'1':'any' }}" @if(isset($schema['minimum'])) min="{{ $schema['minimum'] }}" @endif @if(isset($schema['maximum'])) max="{{ $schema['maximum'] }}" @endif @elseif($decimalString) inputmode="decimal" @endif @if(isset($schema['maxLength'])) maxlength="{{ $schema['maxLength'] }}" @endif aria-describedby="{{ $fieldId }}-help {{ $fieldId }}-error" @if($hasError) aria-invalid="true" @endif>
        @endif
        <span dir="auto" class="field-error" id="{{ $fieldId }}-error">@error($fieldKey)
@php
$localizedMessage=__($message);
@endphp
{{ is_string($localizedMessage)?$localizedMessage:$message }}@enderror</span>
    </div>
@endif
