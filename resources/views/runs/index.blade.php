@extends('layouts.app')
@section('title',__('Saved sessions'))
@section('content')
@php
$formatLocale=($preview ?? false)?'en':($locale ?? 'en');
@endphp
<div class="shell page-space">
    <div class="page-heading"><div><p dir="auto" class="eyebrow">{{ __('YOUR WORK IN PROGRESS') }}</p><h1 dir="auto">{{ __('Pick up the thread.') }}</h1><p dir="auto">{{ __('Saved assessments from your current browser session or signed-in account.') }}</p></div><a dir="auto" class="button button-outline" href="{{ route('home') }}#assessments">{{ __('Start another assessment') }}<span dir="auto" aria-hidden="true"> ↗</span></a></div>
    @include('components.recovery-code')
    <div class="saved-list">
        @forelse($runs as $savedRun)
        <article class="saved-row">
            <div class="saved-symbol" aria-hidden="true">↗</div>
            <div class="saved-main"><span dir="auto" class="tiny-label">{{ $savedRun->flow->name ?? __('Assessment') }}</span><h2 dir="auto">{{ $savedRun->title ?? $savedRun->project_alias ?? __('Your assessment') }}</h2><p dir="auto">{{ __('Last saved :time',['time'=>$savedRun->updated_at ? \App\Domain\LocaleFormat::date($savedRun->updated_at,$formatLocale) : __('recently')]) }} <span dir="auto" aria-hidden="true">·</span> <bdi dir="auto">{{ $savedRun->locale ?? 'en' }}</bdi></p></div>
            <span dir="auto" class="badge">{{ __(\Illuminate\Support\Str::headline($savedRun->status ?? 'active')) }}</span>
            <a dir="auto" class="button button-outline" href="{{ route('runs.show',$savedRun) }}">{{ __('Resume') }}<span dir="auto" class="sr-only"> {{ $savedRun->title ?? $savedRun->project_alias ?? __('assessment') }}</span><span dir="auto" aria-hidden="true"> →</span></a>
        </article>
        @empty
        <div class="empty-state"><span dir="auto" class="empty-symbol" aria-hidden="true">↗</span><p dir="auto" class="eyebrow">{{ __('A FRESH PAGE') }}</p><h2 dir="auto">{{ __('No saved assessments yet') }}</h2><p dir="auto">{{ __('Start with the question on your mind. You can save your answers and return at any point.') }}</p><a dir="auto" class="button button-primary" href="{{ route('home') }}#assessments">{{ __('Choose an assessment') }}</a></div>
        @endforelse
    </div>
    @if(is_object($runs) && method_exists($runs,'links'))<div class="pagination">{{ $runs->links() }}</div>@endif
    <section class="recovery-panel" aria-labelledby="resume-code-title"><div><p dir="auto" class="eyebrow">{{ __('RETURNING ON ANOTHER DEVICE?') }}</p><h2 dir="auto" id="resume-code-title">{{ __('Use your private recovery code.') }}</h2><p dir="auto">{{ __('A code is proof of access. We never display another person\'s sessions from an ID alone.') }}</p></div><form method="post" action="{{ route('resume') }}" data-submitting>@csrf<label dir="auto" for="resume-token">{{ __('Recovery code') }}</label><div class="input-action"><input id="resume-token" name="token" type="text" required autocomplete="off" spellcheck="false" dir="ltr"><button dir="auto" class="button button-primary" type="submit" data-loading-text="{{ __('Checking…') }}">{{ __('Restore session') }}</button></div></form></section>
</div>
@endsection
