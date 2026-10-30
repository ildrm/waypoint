<?php

return [
    'source_locale' => 'en',
    'source_preview' => (bool) env('WAYPOINT_SOURCE_PREVIEW', false),
    'locales' => [
        'en' => ['name' => 'English', 'direction' => 'ltr'], 'fa' => ['name' => 'فارسی', 'direction' => 'rtl'],
        'fr' => ['name' => 'Français', 'direction' => 'ltr'], 'de' => ['name' => 'Deutsch', 'direction' => 'ltr'],
        'ru' => ['name' => 'Русский', 'direction' => 'ltr'], 'hi' => ['name' => 'हिन्दी', 'direction' => 'ltr'],
        'zh' => ['name' => '中文', 'direction' => 'ltr'], 'ja' => ['name' => '日本語', 'direction' => 'ltr'],
        'es' => ['name' => 'Español', 'direction' => 'ltr'], 'pt' => ['name' => 'Português', 'direction' => 'ltr'],
        'ar' => ['name' => 'العربية', 'direction' => 'rtl'], 'tr' => ['name' => 'Türkçe', 'direction' => 'ltr'],
        'hy' => ['name' => 'Հայերեն', 'direction' => 'ltr'], 'it' => ['name' => 'Italiano', 'direction' => 'ltr']],
    'anonymous_retention_days' => 7, 'retention_approved' => (bool) env('RETENTION_POLICY_APPROVED', false),
    'rule_max_depth' => 12, 'rule_max_nodes' => 200, 'max_answer_bytes' => 64000,
    'capabilities' => ['content_author', 'reviewer', 'publisher', 'translator', 'language_reviewer', 'price_editor', 'commercial_approver', 'support', 'privacy_operator', 'security_admin'],
    'ui_copy' => ['nav.home' => 'Assessments', 'nav.saved' => 'Saved assessments', 'nav.help' => 'Help', 'nav.admin' => 'Administration', 'action.next' => 'Next', 'action.back' => 'Back', 'action.save_exit' => 'Save and exit', 'answer.unknown' => 'I do not know yet', 'answer.not_decided' => 'Not decided', 'save.saved' => 'Saved', 'save.failed' => 'Not saved. Retry before leaving.', 'result.partial' => 'Preliminary result — some information needs review.', 'result.limit' => 'Based on your answers and listed assumptions. This is not a guarantee.', 'price.missing' => 'No approved rate is available for this scope.', 'platform.free' => 'Assessments and exports are free. Project estimates are informational.', 'locale.unavailable' => 'This language has not completed content review.', 'review.title' => 'Review your answers', 'export.failed' => 'Your structured report is still available. Retry the export.'],
];
