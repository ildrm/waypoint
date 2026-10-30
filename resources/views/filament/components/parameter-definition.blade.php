@php
    $parameterDefinition=$definition ?? null;
    $definitionConstraints=data_get($parameterDefinition,'constraints',[]) ?? [];
    $definitionEnums=$definitionConstraints['values'] ?? $definitionConstraints['enum'] ?? null;
@endphp
<section class="wp-definition" aria-labelledby="wp-definition-title"><h4 id="wp-definition-title">{{ __('Parameter definition') }}</h4><dl class="wp-meta">
    <dt>{{ __('Type') }}</dt><dd><bdi>{{ data_get($parameterDefinition,'type') ?? __('Not specified') }}</bdi></dd>
    <dt>{{ __('Unit') }}</dt><dd>{{ data_get($parameterDefinition,'unit') ?? __('Not specified') }}</dd>
    <dt>{{ __('Minimum') }}</dt><dd><bdi>{{ array_key_exists('min',$definitionConstraints)?json_encode($definitionConstraints['min'],JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION):__('Not specified') }}</bdi></dd>
    <dt>{{ __('Maximum') }}</dt><dd><bdi>{{ array_key_exists('max',$definitionConstraints)?json_encode($definitionConstraints['max'],JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION):__('Not specified') }}</bdi></dd>
    <dt>{{ __('Allowed enum values') }}</dt><dd><bdi>{{ $definitionEnums!==null?json_encode($definitionEnums,JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION):__('Not applicable') }}</bdi></dd>
    <dt>{{ __('Nullable') }}</dt><dd>{{ data_get($parameterDefinition,'nullable')===null?__('Not specified'):(data_get($parameterDefinition,'nullable')?__('Yes'):__('No')) }}</dd>
    <dt>{{ __('Flow override allowed') }}</dt><dd>{{ data_get($parameterDefinition,'allow_override')===null?__('Not specified'):(data_get($parameterDefinition,'allow_override')?__('Yes'):__('No')) }}</dd>
    <dt>{{ __('Definition revision') }}</dt><dd><bdi>{{ data_get($parameterDefinition,'revision') ?? __('Not specified') }}</bdi></dd>
    <dt>{{ __('Origin') }}</dt><dd><bdi>{{ $definitionOrigin ?? 'general_default' }}</bdi></dd>
</dl><details><summary>{{ __('Full declared constraints') }}</summary><pre>{{ json_encode($definitionConstraints,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION) }}</pre></details></section>
