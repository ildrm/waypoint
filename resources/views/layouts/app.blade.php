@php
    $pageLocale = $locale ?? session('locale', 'en');
    $localeEntries = $locales ?? [];
    $localeEntry = $localeEntries[$pageLocale] ?? [];
    $direction = is_array($localeEntry) ? ($localeEntry['direction'] ?? (in_array($pageLocale, ['fa','ar']) ? 'rtl' : 'ltr')) : (in_array($pageLocale, ['fa','ar']) ? 'rtl' : 'ltr');
    $sourcePreview = (bool) ($preview ?? false);
@endphp
<!doctype html>
<html class="no-js" lang="{{ $sourcePreview ? 'en' : $pageLocale }}" dir="{{ $direction }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="no-referrer">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Waypoint') · Waypoint</title>
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}">
    <script src="{{ asset('assets/app.js') }}" defer></script>
    @stack('head')
</head>
<body class="@yield('body-class')"
    data-ui-offline="{{ __('Offline · changes are not saved') }}"
    data-ui-offline-saved="{{ __('Offline · previous saved version retained') }}"
    data-ui-unsaved="{{ __('Changes not saved') }}"
    data-ui-saving="{{ __('Saving…') }}"
    data-ui-review-saved="{{ __('Review the saved version before making another change') }}"
    data-ui-copy-success="{{ __('Recovery code copied. Keep it private.') }}"
    data-ui-copy-failed="{{ __('Copy is unavailable. Select the displayed recovery code and copy it manually.') }}">
<a dir="auto" class="skip-link" href="#main">{{ __('Skip to content') }}</a>
<header class="site-header">
    <div class="header-inner">
        <a dir="auto" class="brand" href="{{ route('home') }}" aria-label="{{ __('Waypoint home') }}">
            <span dir="auto" class="brand-mark" aria-hidden="true"><span dir="auto"></span><span dir="auto"></span><span dir="auto"></span></span>
            <span dir="auto">waypoint<span dir="auto" class="brand-period">.</span></span>
        </a>
        <nav class="main-nav" aria-label="{{ __('Main navigation') }}">
            <a dir="auto" href="{{ route('home') }}" @class(['is-current'=>request()->routeIs('home')]) @if(request()->routeIs('home')) aria-current="page" @endif>{{ __('Assessments') }}</a>
            <a dir="auto" href="{{ route('runs.index') }}" @class(['is-current'=>request()->routeIs('runs.index')]) @if(request()->routeIs('runs.index')) aria-current="page" @endif>{{ __('Saved sessions') }}</a>
            <a dir="auto" href="{{ route('home') }}#how-it-works">{{ __('How it works') }}</a>
        </nav>
        <div class="header-tools">
            <form class="locale-form" method="post" action="{{ route('locale.switch') }}">
                @csrf
                <label dir="auto" class="sr-only" for="site-locale">{{ __('Interface language') }}</label>
                <select name="locale" id="site-locale">
                    @forelse($localeEntries as $tag=>$entry)
                        <option value="{{ $tag }}" @selected($tag===$pageLocale) lang="{{ $tag }}">{{ is_array($entry) ? ($entry['native'] ?? $entry['name'] ?? $tag) : $entry }}</option>
                    @empty
                        <option value="en">{{ __('English') }}</option>
                    @endforelse
                </select>
                <button dir="auto" class="button button-small button-quiet" type="submit">{{ __('Apply') }}<span dir="auto" class="sr-only"> {{ __('language') }}</span></button>
            </form>
            <a dir="auto" class="admin-link" href="{{ url('/admin') }}">{{ __('Staff') }}<span dir="auto" aria-hidden="true"> ↗</span></a>
        </div>
    </div>
</header>
@if($sourcePreview)
<div class="preview-banner" role="note">
    <div class="shell"><span dir="auto" class="status-dot" aria-hidden="true"></span><strong dir="auto">{{ __('Source preview') }}</strong> <span dir="auto">{{ __('English source content is shown for evaluation. Selected-language content is not yet approved for publication.') }}</span></div>
</div>
@endif
<div id="connection-status" class="connection-banner" role="status" aria-live="polite" hidden>{{ __('You are offline. Changes are not saved until the server confirms them. Keep this page open and retry when connected.') }}</div>
@if(session('status'))
<div class="shell flash-wrap"><div class="notice notice-success" role="status">{{ session('status') }}</div></div>
@endif
@if(session('warning'))
<div class="shell flash-wrap"><div class="notice notice-warning" role="status">{{ session('warning') }}</div></div>
@endif
@if(session('error'))
<div class="shell flash-wrap"><div class="notice notice-error" role="alert">{{ session('error') }}</div></div>
@endif
@if($errors->any())
<div class="shell flash-wrap">
    <section id="error-summary" class="notice notice-error" role="alert" tabindex="-1" data-focus-errors>
        <h2 dir="auto">{{ __('We could not save this yet') }}</h2>
        <p dir="auto">{{ __('Your previous saved version is preserved. Review the details below and try again.') }}</p>
        <ul>@foreach($errors->all() as $error)<li dir="auto">
@php
$localizedError=__($error);
@endphp
{{ is_string($localizedError)?$localizedError:$error }}</li>@endforeach</ul>
    </section>
</div>
@endif
<main id="main" class="site-main" tabindex="-1" @if($sourcePreview) lang="en" @endif>@yield('content')</main>
<footer class="site-footer">
    <div class="shell footer-inner">
        <a dir="auto" class="brand brand-footer" href="{{ route('home') }}">waypoint<span dir="auto" class="brand-period">.</span></a>
        <p dir="auto">{{ __('A clearer next step. Built around your answers.') }}</p>
        <span dir="auto" class="free-note"><span dir="auto" class="small-check" aria-hidden="true">✓</span> {{ __('Assessments and exports are free') }}</span>
    </div>
    <div class="shell footer-bottom"><span dir="auto">{{ __('Project prices are preliminary estimates, not charges for using Waypoint.') }}</span><a dir="auto" href="{{ route('home') }}#help">{{ __('Help & privacy') }}</a></div>
</footer>
@stack('scripts')
</body>
</html>
