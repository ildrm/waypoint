@php
$matching=$matching ?? []; $matchingStatusLabel=['matched'=>__('Matched'),'manual_review'=>__('Manual review'),'no_match'=>__('No match')][$matching['status'] ?? ''] ?? __('Needs review');
@endphp
<div class="matching-result">
    <p dir="auto"><span dir="auto" class="badge {{ in_array($matching['status'] ?? '',['manual_review','no_match'])?'badge-warning':'' }}">{{ $matchingStatusLabel }}</span></p>
    <h3 dir="auto" class="subheading">{{ __('Eligible alternatives') }}</h3>
    @forelse($matching['eligible'] ?? [] as $offering)
        <article class="matching-offering"><div class="matching-offering-heading"><h4 dir="auto">{{ $offering['name'] ?? $offering['stable_id'] ?? __('Eligible alternative') }}</h4>@if($offering['stable_id'] ?? false)<span dir="auto" class="badge"><bdi dir="auto">{{ $offering['stable_id'] }}</bdi></span>@endif</div>
            @if($offering['description'] ?? false)<p dir="auto">{{ $offering['description'] }}</p>@endif
            @if(!empty($offering['reasons']))<h5 dir="auto">{{ __('Reasons for eligibility') }}</h5>@include('components.value',['value'=>$offering['reasons']])@endif
            @if(!empty($offering['deliverables']))<h5 dir="auto">{{ __('Supported deliverables') }}</h5>@include('components.value',['value'=>$offering['deliverables']])@endif
            @if(!empty($offering['dimensions']))<details class="help-disclosure"><summary dir="auto">{{ __('Dimensions and tradeoffs') }}</summary>@include('components.value',['value'=>$offering['dimensions']])</details>@endif
            @if(isset($offering['version']))<p dir="auto" class="small muted">{{ __('Catalog version: :version',['version'=>$offering['version']]) }}</p>@endif
        </article>
    @empty
        <div class="notice notice-quiet"><p dir="auto">{{ __('No approved eligible alternative is available from the recorded context. Review missing evidence and the exclusions below.') }}</p></div>
    @endforelse
    @if(!empty($matching['reasons']))<h3 dir="auto" class="subheading">{{ __('Eligibility reasoning') }}</h3>@include('components.value',['value'=>$matching['reasons']])@endif
    <h3 dir="auto" class="subheading">{{ __('Excluded alternatives and reasons') }}</h3>
    @forelse($matching['excluded'] ?? [] as $offering)
        <article class="matching-exclusion"><h4 dir="auto">{{ $offering['name'] ?? $offering['stable_id'] ?? $offering['id'] ?? __('Excluded alternative') }}</h4>@include('components.value',['value'=>$offering['reason'] ?? $offering['reasons'] ?? __('No exclusion reason is recorded.')])</article>
    @empty<p dir="auto" class="muted">{{ __('No exclusion reason is recorded.') }}</p>@endforelse
    @if(!empty($matching['ties']))<h3 dir="auto" class="subheading">{{ __('Tied alternatives') }}</h3>@include('components.value',['value'=>$matching['ties']])@endif
    @if(!empty($matching['tie_policy']))<div class="notice notice-quiet"><h3 dir="auto">{{ __('How ties are handled') }}</h3><p dir="auto">{{ $matching['tie_policy'] }}</p></div>@endif
</div>
