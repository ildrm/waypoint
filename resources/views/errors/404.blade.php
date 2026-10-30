@extends('layouts.app')
@section('title',__('404 · Request unavailable'))
@section('content')
<div class="shell page-space"><section class="unavailable-sheet" aria-labelledby="error-title"><span dir="auto" class="empty-symbol" aria-hidden="true">↗</span><p dir="auto" class="eyebrow">{{ __('REQUEST 404') }}</p><h1 dir="auto" id="error-title">{{ __('This page is not available.') }}</h1><p dir="auto">{{ __('It may have moved,expired or be outside your current access. Your own saved assessments are the best place to start.') }}</p><div class="notice notice-quiet"><p dir="auto">{{ __('No other user’s project information is displayed here.') }}</p></div><a dir="auto" class="button button-primary" href="{{ route('runs.index') }}">{{ __('Return to saved sessions') }}</a><a dir="auto" class="button button-quiet" href="{{ route('home') }}">{{ __('Choose an assessment') }}</a></section></div>
@endsection
