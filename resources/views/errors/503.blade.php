@extends('layouts.app')
@section('title',__('503 · Request unavailable'))
@section('content')
<div class="shell page-space"><section class="unavailable-sheet" aria-labelledby="error-title"><span dir="auto" class="empty-symbol" aria-hidden="true">↗</span><p dir="auto" class="eyebrow">{{ __('REQUEST 503') }}</p><h1 dir="auto" id="error-title">{{ __('This service needs a moment.') }}</h1><p dir="auto">{{ __('A required part of the service is temporarily unavailable. Please try again shortly.') }}</p><div class="notice notice-quiet"><p dir="auto">{{ __('Your existing report keeps its recorded answers and configuration. We will not substitute a guessed result.') }}</p></div><a dir="auto" class="button button-primary" href="{{ route('runs.index') }}">{{ __('Return to saved sessions') }}</a><a dir="auto" class="button button-quiet" href="{{ route('home') }}">{{ __('Choose an assessment') }}</a></section></div>
@endsection
