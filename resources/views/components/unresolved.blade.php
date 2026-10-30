@php
    $questionLabels=[];
    foreach($recordedAnswers ?? [] as $answerRow){if(is_array($answerRow)&&isset($answerRow['question_id'],$answerRow['question'])&&is_string($answerRow['question']))$questionLabels[$answerRow['question_id']]=$answerRow['question'];}
    $unresolvedRows=is_array($unresolvedItems ?? null)?$unresolvedItems:[$unresolvedItems ?? null];
@endphp
<ul class="value-list unresolved-list">@foreach($unresolvedRows as $item)
@php
    $provenanceId=null;
    if(is_string($item)&&preg_match('/\\bQ-[A-Z0-9-]+\\b/',$item,$matched))$provenanceId=$matched[0];
@endphp
    <li dir="auto">@if($provenanceId && isset($questionLabels[$provenanceId]))<strong dir="auto">{{ $questionLabels[$provenanceId] }}</strong><span class="unresolved-provenance"><bdi dir="auto">{{ $item }}</bdi></span>@else @include('components.value',['value'=>$item])@endif</li>
@endforeach</ul>
