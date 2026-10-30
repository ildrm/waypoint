@if(($isSoftwareReport ?? false) && ($price['synthetic'] ?? false))
<section class="notice notice-warning synthetic-estimate" aria-labelledby="synthetic-estimate-title"><h2 dir="auto" id="synthetic-estimate-title">{{ __('Synthetic price-book example') }}</h2><p dir="auto">{{ __('Synthetic rates and work assumptions are used for demonstration. This is a nonbinding example.') }}</p></section>
@endif
