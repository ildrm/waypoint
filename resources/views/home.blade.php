@extends('layouts.app')
@section('title',__('Find your next useful step'))
@section('body-class', 'home-page')
@section('content')
<div class="shell">
    <section class="hero" aria-labelledby="hero-title">
        <div class="hero-copy">
            <p dir="auto" class="eyebrow"><span dir="auto" class="eyebrow-line" aria-hidden="true"></span> {{ __('Make the next decision a clearer one') }}</p>
            <h1 dir="auto" id="hero-title">{{ __('An idea.') }}<br>{{ __('A few questions.') }}<br><em>{{ __('A way forward.') }}</em></h1>
            <p dir="auto" class="hero-description">{{ __('Understand where you are, what your project needs, and what to do next—with reasoning you can follow.') }}</p>
            <a dir="auto" class="button button-primary" href="#assessments">{{ __('Find your starting point') }}<span dir="auto" aria-hidden="true"> ↓</span></a>
            <p dir="auto" class="hero-reassurance">{{ __('No payment. No contact details needed for a basic result.') }}</p>
        </div>
        <aside class="journey-visual" aria-label="{{ __('How an assessment turns your answers into a useful next step') }}">
            <div class="visual-caption"><span dir="auto" class="tiny-label">{{ __('YOUR PLANNING DESK') }}</span><span dir="auto" class="folio">{{ __('01 / START HERE') }}</span></div>
            <div class="journey-rule" aria-hidden="true"></div>
            <div class="journey-note note-question"><span dir="auto" class="note-index">01</span><p dir="auto" class="tiny-label">{{ __('A QUESTION THAT MATTERS') }}</p><h2 dir="auto">{{ __('What are you') }}<br>{{ __('trying to change?') }}</h2><span dir="auto" class="note-fine">{{ __('Start with the outcome, not a package.') }}</span></div>
            <div class="journey-note note-evidence"><span dir="auto" class="note-index">02</span><p dir="auto" class="tiny-label">{{ __('YOUR CONTEXT') }}</p><div class="evidence-lines"><span dir="auto"><i aria-hidden="true"></i> {{ __('What you know') }}</span><span dir="auto"><i aria-hidden="true"></i> {{ __('What is still uncertain') }}</span><span dir="auto"><i aria-hidden="true"></i> {{ __('What matters most') }}</span></div></div>
            <div class="journey-note note-direction"><span dir="auto" class="note-index">03</span><p dir="auto" class="tiny-label">{{ __('A USEFUL NEXT STEP') }}</p><h2 dir="auto">{{ __('Clear reasons.') }}<br>{{ __('Practical options.') }}</h2><span dir="auto" class="note-arrow" aria-hidden="true">↗</span></div>
            <p dir="auto" class="visual-footnote">{{ __('Your answers lead the way. Uncertainty stays visible.') }}</p>
        </aside>
    </section>
    <section id="assessments" class="assessment-section" aria-labelledby="assessment-title">
        <div class="section-heading"><div><p dir="auto" class="eyebrow">{{ __('CHOOSE A STARTING POINT') }}</p><h2 dir="auto" id="assessment-title">{{ __('What is on your mind?') }}</h2></div><p dir="auto">{{ __('One project at a time.') }}<br> {{ __('Only the questions relevant to you.') }}</p></div>
        <div class="flow-list">
            @forelse($flows as $flow)
                
@php
                    $slug = strtolower($flow->slug ?? '');
                    $isStartup = str_contains($slug, 'startup');
                    $isSoftware = str_contains($slug, 'software');
                    $flowLabel = $flow->name ?? ($isStartup ? __('Startup journey') : ($isSoftware ? __('Software project') : __('Find suitable help')));
                    $description = $flow->description ?? ($isStartup ? __('Turn your evidence, constraints and ambition into priorities you can act on.') : ($isSoftware ? __('Explore scope, delivery surfaces and tradeoffs before commissioning a build.') : __('Compare eligible options against your needs and explain why they fit.')));

@endphp
                <article class="flow-row">
                    <div class="flow-index" aria-hidden="true">{{ str_pad((string) $loop->iteration,2,'0',STR_PAD_LEFT) }}</div>
                    <div class="flow-main"><p dir="auto" class="tiny-label">{{ $isStartup ? __('FOUNDERS & NEW VENTURES') : ($isSoftware ? __('WEBSITES, APPS & SERVICES') : __('PROGRAMS & SERVICES')) }}</p><h3 dir="auto">{{ $flowLabel }}</h3><p dir="auto">{{ $description }}</p><span dir="auto" class="flow-meta">{{ $isStartup ? __('Evidence, priorities & practical actions') : ($isSoftware ? __('Scope, architecture, effort & team') : __('Eligibility, alternatives & next steps')) }}</span></div>
                    <form method="post" action="{{ route('runs.store') }}" class="flow-action" data-submitting>
                        @csrf
                        <input type="hidden" name="flow_id" value="{{ $flow->id }}">
                        <input type="hidden" name="idempotency_key" value="{{ (string)old('flow_id') === (string)$flow->id ? old('idempotency_key',(string)\Illuminate\Support\Str::uuid()) : (string)\Illuminate\Support\Str::uuid() }}">
                        <button dir="auto" type="submit" class="button {{ $loop->first ? 'button-primary' : 'button-outline' }}" data-loading-text="{{ __('Starting…') }}">{{ __('Start assessment') }}<span dir="auto" aria-hidden="true"> ↗</span></button>
                        <span dir="auto" class="flow-action-note">{{ __('Save and return whenever you need') }}</span>
                    </form>
                </article>
            @empty
                <div class="empty-state"><span dir="auto" class="empty-symbol" aria-hidden="true">↗</span><h3 dir="auto">{{ __('Assessments are being prepared') }}</h3><p dir="auto">{{ __('No flow is available in this language yet. Language and content reviews must be complete before publication.') }}</p><a dir="auto" class="text-link" href="{{ url('/admin') }}">{{ __('Staff: review publication readiness') }}</a></div>
            @endforelse
        </div>
    </section>
    <section id="how-it-works" class="how-section" aria-labelledby="how-title">
        <div class="how-intro"><p dir="auto" class="eyebrow">{{ __('A LITTLE STRUCTURE, LESS GUESSWORK') }}</p><h2 dir="auto" id="how-title">{{ __('You do not need') }}<br>{{ __('all the answers.') }}</h2><p dir="auto">{{ __('“I do not know yet” is a useful answer. We keep gaps visible and show the evidence that could improve the result.') }}</p></div>
        <ol class="steps-list"><li dir="auto"><span dir="auto">01</span><div><h3 dir="auto">{{ __('Describe your situation') }}</h3><p dir="auto">{{ __('Work through clear questions with examples, units and room for uncertainty.') }}</p></div></li><li dir="auto"><span dir="auto">02</span><div><h3 dir="auto">{{ __('Review what matters') }}</h3><p dir="auto">{{ __('Check your answers, resolve conflicts and see what is included in the scope.') }}</p></div></li><li dir="auto"><span dir="auto">03</span><div><h3 dir="auto">{{ __('Leave with direction') }}</h3><p dir="auto">{{ __('Explore an explained result, compare a different scope or save a report to discuss.') }}</p></div></li></ol>
    </section>
    <section class="resume-strip" aria-labelledby="resume-title"><div><p dir="auto" class="tiny-label">{{ __('PICK UP WHERE YOU LEFT OFF') }}</p><h2 dir="auto" id="resume-title">{{ __('A clearer view can take more than one sitting.') }}</h2></div><a dir="auto" class="button button-outline" href="{{ route('runs.index') }}">{{ __('View saved sessions') }}<span dir="auto" aria-hidden="true"> →</span></a></section>
    <section id="help" class="help-section" aria-labelledby="help-title"><div><p dir="auto" class="eyebrow">{{ __('HELP & PRIVACY') }}</p><h2 dir="auto" id="help-title">{{ __('Your context stays yours.') }}</h2><p dir="auto">{{ __('Use a project alias. Avoid passwords, payment details, personal records or confidential third-party data in your answers. Results are based on the evidence and assumptions shown, not a promise of business success or a binding quote.') }}</p></div><details class="help-disclosure"><summary dir="auto">{{ __('How saving and recovery work') }}</summary><p dir="auto">{{ __('A saved session belongs to your current browser session or signed-in account. Keep any private recovery code safe. A save is complete only when Waypoint confirms it. You can request data export, deletion or access help through the support form on your report.') }}</p></details><details class="help-disclosure"><summary dir="auto">{{ __('What does an estimate mean?') }}</summary><p dir="auto">{{ __('It is a preliminary estimate of the customer project being assessed. Missing rates or important unknowns can make it incomplete. It is not a charge, contract or payment authorization. Using Waypoint is free.') }}</p></details></section>
</div>
@endsection
