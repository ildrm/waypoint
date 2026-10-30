@if(is_array($value))
    @if($value===[])
        <span dir="auto" class="muted">{{ __('None specified') }}</span>
    @elseif(array_is_list($value))
        <ul class="value-list">@foreach($value as $item)<li dir="auto">@include('components.value',['value'=>$item])</li>@endforeach</ul>
    @else
        <dl class="value-map">@foreach($value as $key=>$item)<div><dt dir="auto">
@php
$valueKeyLabel=\Illuminate\Support\Str::headline((string)$key);$translatedValueKey=__($valueKeyLabel);
@endphp
{{ is_string($translatedValueKey)?$translatedValueKey:$valueKeyLabel }}</dt><dd dir="auto">@include('components.value',['value'=>$item])</dd></div>@endforeach</dl>
    @endif
@elseif(is_bool($value))
    {{ $value ? __('Yes') : __('No') }}
@elseif($value===null || $value==='')
    <span dir="auto" class="muted">{{ __('Not known / not decided') }}</span>
@else
    <bdi dir="auto">{{ $value }}</bdi>
@endif
