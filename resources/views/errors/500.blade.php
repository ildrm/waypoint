@extends('layouts.app')
@section('title',__('500 · Request unavailable'))
@section('content')
<div class="shell page-space"><section class="unavailable-sheet" aria-labelledby="error-title"><span dir="auto" class="empty-symbol" aria-hidden="true">↗</span><p dir="auto" class="eyebrow">{{ __('REQUEST 500') }}</p><h1 dir="auto" id="error-title">{{ __('We could not complete that action.') }}</h1><p dir="auto">{{ __('Your last acknowledged save is the version to rely on. Return to saved sessions and retry the specific action.') }}</p><div class="notice notice-quiet"><p dir="auto">{{ __('A failed export does not remove its structured report. Do not include recovery codes or sensitive records in support requests.') }}</p></div><a dir="auto" class="button button-primary" href="{{ route('runs.index') }}">{{ __('Return to saved sessions') }}</a><a dir="auto" class="button button-quiet" href="{{ route('home') }}">{{ __('Choose an assessment') }}</a></section></div>
@endsection
