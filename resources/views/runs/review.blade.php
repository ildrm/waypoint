@extends('layouts.app')
@section('title',__('Review your answers'))
@section('content')
@php
    $formatLocale=($preview ?? false)?'en':($locale ?? 'en');
    $answerMap=$answers ?? [];
    $activeQuestionIds=$activeIds ?? array_column($bank['questions'] ?? [],'id');
    $activeQuestions=array_values(array_filter($bank['questions'] ?? [],fn($q)=>in_array($q['id'],$activeQuestionIds,true)));
    $unresolvedCount=count(array_filter($activeQuestions,fn($q)=>($answerMap[$q['id']]['state'] ?? 'unanswered')!=='known'));
@endphp
<div class="shell page-space">
    <div class="page-heading"><div><p dir="auto" class="eyebrow">{{ __('PAUSE. CHECK. MOVE FORWARD.') }}</p><h1 dir="auto">{{ __('Does this sound like') }}<br>{{ __('your project?') }}</h1><p dir="auto">{{ __('Review the context your result will use. Edit anything that needs a closer look.') }}</p></div><span dir="auto" class="save-indicator">{{ __('Saved :time',['time'=>$run->updated_at ? \App\Domain\LocaleFormat::date($run->updated_at,$formatLocale) : '']) }}</span></div>
    @if(!empty($conflicts))
        <section class="notice notice-warning" aria-labelledby="conflicts-title"><h2 dir="auto" id="conflicts-title">{{ __('A few answers need clarification') }}</h2><p dir="auto">{{ __('You can correct them now. If a material conflict remains, the report will show a partial or manual-review result.') }}</p><ul>@foreach($conflicts as $conflict)<li dir="auto">@include('components.value',['value'=>$conflict])</li>@endforeach</ul></section>
    @elseif($unresolvedCount)
        <div class="notice notice-quiet"><strong dir="auto">{{ __('Uncertain applicable answers: :count.',['count'=>\App\Domain\LocaleFormat::number($unresolvedCount,$formatLocale)]) }}</strong><p dir="auto">{{ __('That is okay. Your result will show the limitations and what evidence could make it more useful.') }}</p></div>
    @endif
    <div class="review-layout">
        <section class="review-answer-list" aria-label="{{ __('Applicable answers') }}">
            
@php
$lastStage=null;
@endphp
            @forelse($activeQuestions as $q)
                @if(($q['stage'] ?? '')!==$lastStage)
                    
@php
$lastStage=$q['stage'] ?? '';
@endphp
                    <h2 dir="auto" class="review-stage-title">{{ $lastStage ?: __('Your context') }}</h2>
                @endif
                
@php
$item=$answerMap[$q['id']] ?? ['state'=>'unanswered','value'=>null];
@endphp
                <article class="review-answer">
                    <div class="review-answer-heading"><h3 dir="auto">{{ $q['wording'] }}</h3><a dir="auto" class="text-link" href="{{ route('runs.show',[$run,'question'=>$q['id']]) }}">{{ __('Edit') }}<span dir="auto" class="sr-only"> {{ $q['wording'] }}</span></a></div>
                    @if(($item['state'] ?? '')==='known')
                        
@php
    $formatLocale=($preview ?? false)?'en':($locale ?? 'en');
                            $displayValue=$item['display_value'] ?? $item['value'] ?? null;
                            $hasDisplayValue=array_key_exists('display_value',$item);
                            if(!$hasDisplayValue && ($q['input_type'] ?? '')==='select_one'){$option=collect($q['options'] ?? [])->first(fn($o)=>($o['value'] ?? null)===$displayValue);$displayValue=$option['label'] ?? $displayValue;}
                            elseif(!$hasDisplayValue && ($q['input_type'] ?? '')==='select_many' && is_array($displayValue)){$displayValue=array_map(fn($v)=>(collect($q['options'] ?? [])->first(fn($o)=>($o['value'] ?? null)===$v)['label'] ?? $v),$displayValue);}

@endphp
                        <div class="review-value">@include('components.value',['value'=>$displayValue])</div>
                    @else
                        <span dir="auto" class="badge badge-warning">{{ __(\Illuminate\Support\Str::headline($item['state'] ?? 'unanswered')) }}</span>
                    @endif
                </article>
            @empty
                <div class="empty-state"><h2 dir="auto">{{ __('No applicable answers yet') }}</h2><p dir="auto">{{ __('Start with your context, then come back here to review.') }}</p><a dir="auto" class="button button-primary" href="{{ route('runs.show',$run) }}">{{ __('Continue assessment') }}</a></div>
            @endforelse
        </section>
        <aside class="review-sidebar">
            <div class="summary-sheet"><p dir="auto" class="tiny-label">{{ __('YOUR NEXT STEP') }}</p><h2 dir="auto">{{ __('An explained result,') }}<br>{{ __('not a leap of faith.') }}</h2><p dir="auto">{{ __('We will use the applicable answers shown here. Unknowns and assumptions stay visible.') }}</p><ul class="check-list"><li dir="auto">{{ __('Clear reasons and source answers') }}</li><li dir="auto">{{ __('Relevant options and next actions') }}</li><li dir="auto">{{ __('Explicit gaps and limitations') }}</li></ul><form method="post" action="{{ route('reports.store',$run) }}" data-submitting>@csrf<input type="hidden" name="expected_revision" value="{{ $run->revision }}"><input type="hidden" name="idempotency_key" value="{{ old('idempotency_key',(string)\Illuminate\Support\Str::uuid()) }}"><button dir="auto" type="submit" class="button button-primary button-block" data-loading-text="{{ __('Preparing your result…') }}">{{ __('Generate preliminary result') }}<span dir="auto" aria-hidden="true"> ↗</span></button></form><p dir="auto" class="small muted">{{ __('Free assessment. No payment or contract.') }}</p></div>
            <p dir="auto" class="small muted">{{ __('Changing an answer creates a new input revision. Previously generated reports keep their original answers and prices.') }}</p>
        </aside>
    </div>
</div>
@endsection
