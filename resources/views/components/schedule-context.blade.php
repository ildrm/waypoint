@if(isset($schedule['peak_interval']))
    <p dir="auto" class="small muted schedule-peak-interval">{{ __('Peak contributor count interval:') }} <bdi dir="auto">{{ $schedule['peak_interval']==='calendar_week'?__('Calendar week'):\Illuminate\Support\Str::headline((string)$schedule['peak_interval']) }}</bdi></p>
@endif
@if(!empty($schedule['assumptions']))<h3 dir="auto" class="subheading">{{ __('Schedule assumptions') }}</h3>@include('components.value',['value'=>$schedule['assumptions']])@endif
