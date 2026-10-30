@php
    $capacityRowKey=$fieldKey.'.'.$capacityIndex;
    $capacityRowName=$fieldName.'['.$capacityIndex.']';
    $capacityRowId='capacity-row-'.$capacityIndex;
    $capacityRowErrors=collect($errors->get($capacityRowKey.'.*'))->flatten()->unique()->map(static function($message){$localized=__($message);return is_string($localized)?$localized:$message;})->all();
    $capacityFields=['id'=>['label'=>'Contributor alias','type'=>'text'],'roles'=>['label'=>'Role IDs, separated by commas','type'=>'text'],'hours_week'=>['label'=>'Combined available hours per week','type'=>'number'],'start_week'=>['label'=>'First available project week','type'=>'number'],'end_week'=>['label'=>'Last available project week','type'=>'number'],'holiday_weeks'=>['label'=>'Unavailable week numbers, separated by commas','type'=>'text']];
@endphp
<fieldset class="capacity-row" aria-describedby="{{ $capacityRowId }}-error"><legend dir="auto">{{ __('Contributor :number',['number'=>\App\Domain\LocaleFormat::number($capacityIndex+1,($preview ?? false)?'en':($locale ?? 'en'))]) }}</legend><div class="capacity-field-grid">
@foreach($capacityFields as $key=>$meta)
    
@php
$rowValue=$capacityRow[$key] ?? '';if(is_array($rowValue))$rowValue=implode(', ',array_filter($rowValue,'is_scalar'));
@endphp
    <div class="field-group"><label dir="auto" for="{{ $capacityRowId }}-{{ $key }}">{{ __($meta['label']) }}</label><input type="{{ $meta['type'] }}" dir="{{ $meta['type']==='number'?'ltr':'auto' }}" id="{{ $capacityRowId }}-{{ $key }}" name="{{ $capacityRowName }}[{{ $key }}]" value="{{ is_scalar($rowValue)?$rowValue:'' }}" @if($meta['type']==='number') inputmode="{{ $key==='hours_week'?'decimal':'numeric' }}" min="{{ $key==='hours_week'?'0':'1' }}" step="{{ $key==='hours_week'?'any':'1' }}" @else maxlength="1000" @endif aria-describedby="{{ $capacityRowId }}-error" @if($errors->has($capacityRowKey.'.'.$key)) aria-invalid="true" @endif></div>
@endforeach
</div><span dir="auto" class="field-error" id="{{ $capacityRowId }}-error">{{ implode(' ',$capacityRowErrors) }}</span></fieldset>
