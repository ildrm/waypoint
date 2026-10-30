@extends('layouts.app')
@section('title',__('419 · Request unavailable'))
@section('content')
<div class="shell page-space"><section class="unavailable-sheet" aria-labelledby="error-title"><span dir="auto" class="empty-symbol" aria-hidden="true">↗</span><p dir="auto" class="eyebrow">{{ __('REQUEST 419') }}</p><h1 dir="auto" id="error-title">{{ __('Your secure session has expired.') }}</h1><p dir="auto">{{ __('Open the assessment again from your saved sessions before submitting another change.') }}</p><div class="notice notice-quiet"><p dir="auto">{{ __('The last acknowledged save remains the version to rely on. Unsaved entries may still be available through your browser’s Back action.') }}</p></div><a dir="auto" class="button button-primary" href="{{ route('runs.index') }}">{{ __('Return to saved sessions') }}</a><a dir="auto" class="button button-quiet" href="{{ route('home') }}">{{ __('Choose an assessment') }}</a></section></div>
@endsection
