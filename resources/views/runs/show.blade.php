@extends('layouts.app')
@section('title', $question['wording'] ?? __('Your assessment'))
@section('body-class','question-page')
@section('content')
@php
    $formatLocale=($preview ?? false)?'en':($locale ?? 'en');
    $savedAnswer = $answer ?? [];
    $savedState = $savedAnswer['state'] ?? 'unanswered';
    $savedValue = old('value',$savedAnswer['value'] ?? null);
    $answeredCount = (int)($progress['answered'] ?? 0);
    $activeCount = max(1,(int)($progress['total'] ?? 1));
    $previousQuestion = $previousId ?? null;
    $questionOptions = $question['options'] ?? [];
@endphp
<div class="shell page-space">
    <div class="question-topline"><a dir="auto" class="back-link" href="{{ route('runs.index') }}"><span dir="auto" aria-hidden="true">←</span> {{ __('Saved sessions') }}</a><span dir="auto" class="save-indicator" data-save-status role="status" aria-live="polite">{{ __('Saved :time',['time'=>$run->updated_at ? \App\Domain\LocaleFormat::date($run->updated_at,$formatLocale) : '']) }}</span></div>
    @include('components.recovery-code')
    <div class="assessment-workspace">
        <aside class="assessment-sidebar" aria-label="{{ __('Assessment context') }}">
            <span dir="auto" class="tiny-label">{{ __('YOUR ASSESSMENT') }}</span><h2 dir="auto">{{ $run->flow->name ?? $bank['name'] ?? ucfirst(strtolower(str_replace('WF-','',$bank['workflow_id'] ?? __('Assessment')))) }}</h2>
            <div class="progress-block"><span dir="auto">{{ __('Answered :answered of :total currently applicable questions',['answered'=>\App\Domain\LocaleFormat::number($answeredCount,$formatLocale),'total'=>\App\Domain\LocaleFormat::number($activeCount,$formatLocale)]) }}</span><progress value="{{ min($answeredCount,$activeCount) }}" max="{{ $activeCount }}">{{ __(':answered of :total',['answered'=>\App\Domain\LocaleFormat::number($answeredCount,$formatLocale),'total'=>\App\Domain\LocaleFormat::number($activeCount,$formatLocale)]) }}</progress></div>
            <p dir="auto" class="small muted">{{ __('Follow-up questions may change the total as we learn about your project.') }}</p>
            <a dir="auto" class="text-link sidebar-review" href="{{ route('runs.review',$run) }}">{{ __('Review your answers') }}<span dir="auto" aria-hidden="true"> ↗</span></a>
            <div class="sidebar-note"><span dir="auto" class="small-check" aria-hidden="true">✓</span><p dir="auto">{{ __('You do not need to know everything. Unknown answers stay visible in your result.') }}</p></div>
        </aside>
        <section class="question-stage" aria-labelledby="question-title">
            <p dir="auto" class="eyebrow">{{ $question['stage'] ?? __('Your context') }}</p>
            <h1 dir="auto" id="question-title" class="question-title" tabindex="-1" data-question-heading>{{ $question['wording'] ?? __('Tell us about your project') }}</h1>
            @if($question['help'] ?? false)<p dir="auto" class="question-help" id="question-help">{{ $question['help'] }}</p>@endif
            @if(in_array($savedState,['unknown','not_decided','inactive']))
                <div class="notice notice-quiet"><strong dir="auto">{{ $savedState==='inactive'?__('This answer needs reconfirmation'):($savedState==='not_decided'?__('Previously marked: not decided'):__('Previously marked: not known')) }}</strong><p dir="auto">{{ $savedState==='inactive'?__('Your earlier selection became inactive after a related change. Review it before using it again.'):__('You can add detail now or keep this uncertainty in the report.') }}</p></div>
            @endif
            @include('components.clarifications',['clarifications'=>$question['clarifications'] ?? []])
            <form method="post" action="{{ route('answers.store',$run) }}" class="question-form" data-answer-form data-submitting data-loading-scope>
                @csrf
                <input type="hidden" name="question_id" value="{{ $question['id'] }}">
                <input type="hidden" name="expected_revision" value="{{ $run->revision }}">
                <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key',(string)\Illuminate\Support\Str::uuid()) }}">
                <fieldset class="question-inputs" @if($question['help'] ?? false) aria-describedby="question-help" @endif>
                    <legend dir="auto" class="sr-only">{{ $question['wording'] }}</legend>
                    @if(in_array($question['input_type'] ?? '',['select_one','select_many']))
                        
@php
$multiple=($question['input_type']==='select_many');
@endphp
                        @if($multiple)<p dir="auto" class="selection-hint">{{ __('Select every applicable option. “None” cannot be combined with another option.') }}</p>@endif
                        <div class="choice-list" @if($errors->has('value') || $errors->has('value.*')) aria-describedby="answer-value-error" @endif data-choice-group="{{ $multiple?'multiple':'single' }}">
                        @foreach($questionOptions as $option)
                            
@php
    $formatLocale=($preview ?? false)?'en':($locale ?? 'en');
                                $selected=$multiple?(is_array($savedValue)&&in_array($option['value'],$savedValue,true)):($savedValue!==null&&(string)$savedValue===(string)$option['value']);

@endphp
                            <label class="choice" for="option-{{ $loop->index }}">
                                <input id="option-{{ $loop->index }}" type="{{ $multiple?'checkbox':'radio' }}" name="{{ $multiple?'value[]':'value' }}" value="{{ $option['value'] }}" @checked($selected) @if($errors->has('value') || $errors->has('value.*')) aria-invalid="true" aria-describedby="answer-value-error" @endif @if(($option['value'] ?? '')==='none') data-exclusive @endif>
                                <span dir="auto" class="choice-copy"><span dir="auto" class="choice-label">{{ $option['label'] }}</span>@if($option['description'] ?? false)<span dir="auto" class="choice-description">{{ $option['description'] }}</span>@endif</span>
                            </label>
                        @endforeach
                        </div>
                        @if($errors->has('value') || $errors->has('value.*'))<p dir="auto" class="field-error" id="answer-value-error" role="alert">
@php
$choiceError=$errors->first('value') ?: $errors->first('value.*');$localizedChoiceError=__($choiceError);
@endphp
{{ is_string($localizedChoiceError)?$localizedChoiceError:$choiceError }}</p>@endif
                    @else
                        @include('components.schema-field',['schema'=>$question['schema'] ?? [],'fieldName'=>'value','fieldKey'=>'value','fieldLabel'=>'Your answer','fieldValue'=>$savedValue])
                    @endif
                </fieldset>
                <details class="help-disclosure question-context"><summary dir="auto">{{ __('Why we ask this') }}</summary><p dir="auto">{{ $question['purpose'] ?? __('This helps us make a relevant recommendation and explain the assumptions behind it.') }}</p>@if($question['unit_period_range'] ?? false)<p dir="auto" class="small">{{ $question['unit_period_range'] }}</p>@endif</details>
                <div class="uncertainty-actions"><button dir="auto" type="submit" name="state" value="unknown" class="text-button" formnovalidate data-loading-text="{{ __('Saving…') }}">{{ __('I do not know yet') }}<span dir="auto" aria-hidden="true"> ↗</span></button><button dir="auto" type="submit" name="state" value="not_decided" class="text-button" formnovalidate data-loading-text="{{ __('Saving…') }}">{{ __('Not decided') }}</button></div>
                <div class="question-navigation">
                    @if($previousQuestion)<a dir="auto" href="{{ route('runs.show',[$run,'question'=>$previousQuestion]) }}" class="back-link"><span dir="auto" aria-hidden="true">←</span> {{ __('Back') }}</a>@else<a dir="auto" href="{{ route('runs.index') }}" class="back-link">{{ __('Return to saved sessions') }}</a>@endif
                    <div class="question-save-actions"><button dir="auto" type="submit" name="state" value="known" formaction="{{ route('answers.store',[$run,'intent'=>'exit']) }}" class="button button-outline" data-loading-text="{{ __('Saving…') }}">{{ __('Save & exit') }}</button><button dir="auto" type="submit" name="state" value="known" class="button button-primary" data-loading-text="{{ __('Saving…') }}">{{ __('Save & continue') }}<span dir="auto" aria-hidden="true"> →</span></button></div>
                </div>
                <details class="help-disclosure exit-uncertainty"><summary dir="auto">{{ __('Save an uncertain answer and exit') }}</summary><p dir="auto">{{ __('Choose an explicit uncertainty state. Unfinished field values will not be treated as a known answer.') }}</p><div class="uncertainty-actions"><button dir="auto" type="submit" name="state" value="unknown" formaction="{{ route('answers.store',[$run,'intent'=>'exit']) }}" class="text-button" formnovalidate data-loading-text="{{ __('Saving…') }}">{{ __('Save as not known & exit') }}</button><button dir="auto" type="submit" name="state" value="not_decided" formaction="{{ route('answers.store',[$run,'intent'=>'exit']) }}" class="text-button" formnovalidate data-loading-text="{{ __('Saving…') }}">{{ __('Save as not decided & exit') }}</button></div></details>
                <p dir="auto" class="form-footer-note">{{ __('Your answer is saved only after the server confirms it. Changing an earlier answer can remove dependent items from a new result.') }}</p>
            </form>
        </section>
    </div>
</div>
@endsection
