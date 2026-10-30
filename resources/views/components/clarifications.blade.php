@if(!empty($clarifications))
<section class="notice notice-warning clarification-panel" aria-labelledby="question-clarifications-title"><h2 dir="auto" id="question-clarifications-title">{{ __('Review the answers behind this clarification') }}</h2>
    @foreach($clarifications as $clarification)<article class="clarification-item">@if(!empty($clarification['message']))<p dir="auto">{{ $clarification['message'] }}</p>@endif
        <ul class="clarification-sources">@foreach($clarification['answers'] ?? [] as $sourceAnswer)<li><div class="clarification-source-heading"><h3 dir="auto">{{ $sourceAnswer['wording'] ?? $sourceAnswer['id'] ?? __('Source question') }}</h3>@if(!empty($sourceAnswer['id']))<a dir="auto" class="text-link" href="{{ route('runs.show',[$run,'question'=>$sourceAnswer['id']]) }}">{{ __('Edit this answer') }}<span dir="auto" class="sr-only"> {{ $sourceAnswer['wording'] ?? $sourceAnswer['id'] }}</span></a>@endif</div><div class="clarification-selected">@include('components.value',['value'=>$sourceAnswer['label'] ?? null])</div>@if(!empty($sourceAnswer['id']))<p dir="auto" class="clarification-source-id"><bdi dir="auto">{{ $sourceAnswer['id'] }}</bdi></p>@endif</li>@endforeach</ul>
    </article>@endforeach
</section>
@endif
