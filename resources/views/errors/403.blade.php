@extends('layouts.app')
@section('title',__('403 · Request unavailable'))
@section('content')
<div class="shell page-space"><section class="unavailable-sheet" aria-labelledby="error-title"><span dir="auto" class="empty-symbol" aria-hidden="true">↗</span><p dir="auto" class="eyebrow">{{ __('REQUEST 403') }}</p><h1 dir="auto" id="error-title">{{ __('Access is restricted.') }}</h1><p dir="auto">{{ __('Your current permissions do not allow this action. Sign in with the appropriate account or return to your own saved sessions.') }}</p><div class="notice notice-quiet"><p dir="auto">{{ __('We do not grant access through a report or project ID alone.') }}</p></div><a dir="auto" class="button button-primary" href="{{ route('runs.index') }}">{{ __('Return to saved sessions') }}</a><a dir="auto" class="button button-quiet" href="{{ route('home') }}">{{ __('Choose an assessment') }}</a></section></div>
@endsection
