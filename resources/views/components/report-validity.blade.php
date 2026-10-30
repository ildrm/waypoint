@if($validUntil ?? null)
    <div class="report-validity"><p dir="auto" class="small muted">{{ __('Valid until :time (:zone)',['time'=>\App\Domain\LocaleFormat::date($validUntil,$formatLocale ?? 'en'),'zone'=>config('app.timezone','UTC')]) }}</p>
    @if($isExpired ?? false)<section class="notice notice-warning" aria-labelledby="report-expired-title"><h2 dir="auto" id="report-expired-title">{{ __('Expired historical report') }}</h2><p dir="auto">{{ __('This report is kept as a historical record. Its original inputs and results are preserved. Create a fresh report to review current assumptions.') }}</p></section>@endif
    </div>
@endif
