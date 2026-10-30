@extends('layouts.app')
@section('title',__('412 · Request unavailable'))
@section('content')
<div class="shell page-space"><section class="unavailable-sheet" aria-labelledby="error-title"><span dir="auto" class="empty-symbol" aria-hidden="true">↗</span><p dir="auto" class="eyebrow">{{ __('REQUEST 412') }}</p><h1 dir="auto" id="error-title">{{ __('This assessment changed elsewhere.') }}</h1><p dir="auto">{{ __('The saved version is newer than the version you were editing. Review the saved answers before trying again.') }}</p><div class="notice notice-quiet"><p dir="auto">Your attempted change was not applied. Use your browser’s Back action to inspect any unsaved entry;do not assume it has been saved.</p></div><a dir="auto" class="button button-primary" href="{{ route('runs.index') }}">{{ __('Return to saved sessions') }}</a><a dir="auto" class="button button-quiet" href="{{ route('home') }}">{{ __('Choose an assessment') }}</a></section></div>
@endsection
