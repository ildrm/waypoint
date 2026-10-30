@php
    $capacityInput=is_array($fieldValue)?$fieldValue:($fieldValue!==null && $fieldValue!==''?[$fieldValue]:[]);
    $capacityRows=[];$legacyCapacity=[];
    foreach($capacityInput as $index=>$entry){
        $rowIndex=is_numeric($index)?(int)$index:count($capacityRows);
        if(is_array($entry)){$capacityRows[$rowIndex]=$entry;continue;}
        if(!is_string($entry)||trim($entry)==='')continue;
        $decoded=json_decode($entry,true);
        if(is_array($decoded)&&array_key_exists('id',$decoded)&&array_key_exists('roles',$decoded))$capacityRows[$rowIndex]=$decoded;
        else $legacyCapacity[]=$entry;
    }
    $capacityRowCount=min(100,max(10,$capacityRows?max(array_keys($capacityRows))+1:0));
    $extraHasData=false;foreach($capacityRows as $index=>$row){if($index>=6&&count(array_filter($row,fn($v)=>$v!==null&&$v!==''&&$v!==[])))$extraHasData=true;}
    $legacyCapacityText=old('value.capacity_legacy',implode("\n",$legacyCapacity));
@endphp
<fieldset class="capacity-editor" aria-describedby="capacity-guidance capacity-error">
    <legend dir="auto">{{ $translatedFieldLabel }}</legend>
    <p dir="auto" id="capacity-guidance" class="field-help">{{ __('Use one row per distinct contributor. Use a neutral alias, not a personal name. If one person covers several roles, list all their roles in one row and enter their combined available hours once.') }}</p>
    <p dir="auto" class="field-help">{{ __('Leave unused rows blank. Enter role IDs separated by commas. First and last week refer to the project calendar; unavailable weeks are comma-separated week numbers. Leave unavailable weeks blank if there are none. No hours or dates are assumed.') }}</p>
    <div class="capacity-rows">@for($capacityIndex=0;$capacityIndex<6;$capacityIndex++)@include('components.capacity-row',['capacityRow'=>$capacityRows[$capacityIndex] ?? [],'capacityIndex'=>$capacityIndex])@endfor</div>
    <details class="help-disclosure capacity-extra" @if($extraHasData) open @endif><summary dir="auto">{{ __('More contributors (optional)') }}</summary><div class="capacity-rows">@for($capacityIndex=6;$capacityIndex<$capacityRowCount;$capacityIndex++)@include('components.capacity-row',['capacityRow'=>$capacityRows[$capacityIndex] ?? [],'capacityIndex'=>$capacityIndex])@endfor</div></details>
    @if($legacyCapacityText!=='')<details class="help-disclosure" open><summary dir="auto">{{ __('Previously saved availability notes') }}</summary><p dir="auto" class="field-help" id="capacity-legacy-help">{{ __('These notes are preserved separately. To use a contributor in a schedule, enter their alias, roles and calendar in a row above. Freeform notes do not establish available capacity.') }}</p><label dir="auto" for="capacity-legacy">{{ __('Existing availability notes') }}</label><textarea dir="auto" name="value[capacity_legacy]" id="capacity-legacy" rows="4" aria-describedby="capacity-legacy-help">{{ $legacyCapacityText }}</textarea></details>@endif
    <span dir="auto" class="field-error" id="capacity-error">
@php
$capacityError=$errors->first($fieldKey);$localizedCapacityError=__($capacityError);
@endphp
{{ is_string($localizedCapacityError)?$localizedCapacityError:$capacityError }}</span>
</fieldset>
