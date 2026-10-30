@php
    $features=$featureMatrix ?? [];
    $scopePhases=$phases ?? [];
    $solutionArchitecture=$architecture ?? [];
    $displayLocale=$formatLocale ?? $locale ?? 'en';
    $phaseLabels=['base'=>__('Base scope'),'optional'=>__('Optional scope'),'to_confirm'=>__('To confirm')];
@endphp
@if($features)
    <h3 dir="auto" class="subheading">{{ __('Feature and delivery matrix') }}</h3>
    <div class="table-region" role="region" aria-label="{{ __('Features, quantities and delivery surfaces') }}" tabindex="0"><table class="feature-matrix"><caption dir="auto">{{ __('Recorded features and confirmation state') }}</caption><thead><tr><th dir="auto" scope="col">{{ __('Feature') }}</th><th dir="auto" scope="col">{{ __('Quantity / unit') }}</th><th dir="auto" scope="col">{{ __('Delivery surfaces') }}</th><th dir="auto" scope="col">{{ __('Phase') }}</th></tr></thead><tbody>
    @foreach($features as $feature)<tr><th dir="auto" scope="row">{{ $feature['name'] ?? $feature['id'] ?? __('Feature') }}@if(!empty($feature['id']))<small class="feature-id"><bdi dir="auto">{{ $feature['id'] }}</bdi></small>@endif<details class="feature-evidence"><summary dir="auto">{{ __('Source & acceptance') }}</summary>@include('components.value',['value'=>$feature['sources'] ?? []])@if(!empty($feature['acceptance']))<p dir="auto">{{ $feature['acceptance'] }}</p>@endif</details></th><td dir="auto">@if(($feature['quantity'] ?? null)!==null)<bdi dir="auto">{{ is_numeric($feature['quantity'])?\App\Domain\LocaleFormat::number($feature['quantity'],$displayLocale):$feature['quantity'] }}</bdi>@else<span dir="auto" class="muted">{{ __('To confirm') }}</span>@endif @if(!empty($feature['unit']))<span dir="auto" class="feature-unit">{{ $feature['unit'] }}</span>@endif</td><td dir="auto">@include('components.value',['value'=>$feature['surfaces'] ?? []])</td><td dir="auto"><span dir="auto" class="badge {{ ($feature['phase'] ?? '')==='to_confirm'?'badge-warning':'' }}">{{ $phaseLabels[$feature['phase'] ?? 'to_confirm'] ?? \Illuminate\Support\Str::headline($feature['phase']) }}</span></td></tr>@endforeach
    </tbody></table></div>
@endif
@if($scopePhases)
    <div class="phase-groups">@foreach(['base','optional','to_confirm'] as $phase)
@php
$phaseItems=$scopePhases[$phase] ?? [];
@endphp
<details class="help-disclosure"><summary dir="auto">{{ $phaseLabels[$phase] }} <span dir="auto" class="phase-count">({{ \App\Domain\LocaleFormat::number(count($phaseItems),$displayLocale) }})</span></summary>@if($phaseItems)<ul class="value-list">@foreach($phaseItems as $item)<li dir="auto">{{ is_array($item)?($item['name'] ?? $item['id'] ?? __('Feature')):$item }}</li>@endforeach</ul>@else<p dir="auto">{{ __('No feature is assigned to this phase.') }}</p>@endif</details>@endforeach</div>
@endif
@if(!empty($solutionArchitecture['constraints']))
    <details class="help-disclosure scope-evidence"><summary dir="auto">{{ __('Architecture constraints and evidence') }}</summary><p dir="auto">{{ __('Compare each candidate with the recorded constraints. An unanswered constraint remains an open question.') }}</p>@foreach($solutionArchitecture['constraints'] as $constraint)<article class="constraint-evidence"><div class="constraint-heading"><h4 dir="auto"><bdi dir="auto">{{ $constraint['question_id'] ?? __('Constraint') }}</bdi></h4><span dir="auto" class="badge {{ ($constraint['state'] ?? '')!=='known'?'badge-warning':'' }}">{{ __(\Illuminate\Support\Str::headline($constraint['state'] ?? 'unanswered')) }}</span></div>@include('components.value',['value'=>$constraint['value'] ?? null])@if(!empty($constraint['impact']))<p dir="auto" class="small muted">{{ $constraint['impact'] }}</p>@endif</article>@endforeach</details>
@endif
@if(!empty($solutionArchitecture['rejected_candidates']))
    <details class="help-disclosure scope-evidence"><summary dir="auto">{{ __('Candidates not supported by this evidence') }}</summary>@foreach($solutionArchitecture['rejected_candidates'] as $candidate)<article class="constraint-evidence"><h4 dir="auto">{{ $candidate['candidate'] ?? __('Candidate') }}</h4><p dir="auto">{{ $candidate['reason'] ?? __('No reason is recorded.') }}</p></article>@endforeach</details>
@endif
