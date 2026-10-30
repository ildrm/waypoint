@extends('layouts.app')
@section('title',__('429 · Request unavailable'))
@section('content')
<div class="shell page-space"><section class="unavailable-sheet" aria-labelledby="error-title"><span dir="auto" class="empty-symbol" aria-hidden="true">↗</span><p dir="auto" class="eyebrow">{{ __('REQUEST 429') }}</p><h1 dir="auto" id="error-title">{{ __('A short pause is needed.') }}</h1><p dir="auto">{{ __('Too many requests arrived in a short period. Wait briefly before trying this action again.') }}</p><div class="notice notice-quiet"><p dir="auto">{{ __('Your saved reports are not repriced or rewritten by a retry. Using Waypoint remains free.') }}</p></div><a dir="auto" class="button button-primary" href="{{ route('runs.index') }}">{{ __('Return to saved sessions') }}</a><a dir="auto" class="button button-quiet" href="{{ route('home') }}">{{ __('Choose an assessment') }}</a></section></div>
@endsection
