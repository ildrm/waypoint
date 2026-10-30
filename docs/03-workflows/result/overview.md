# WF-RESULT procedure

Owner: R22. Reviewers: R05, R23, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-RESULT, AC-01–16. Open decisions: [register](../../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../../00-governance/skills/workflow-questionnaire.md).

Trigger/actors: Owner opens review or requests report/export.

Preconditions/permissions: Authorized run/revision; active facts and conflict state known.

States: Review -> partial/ready -> calculating -> generated -> export_pending -> export_ready/failed -> superseded.

Form: run_revision, locale, report_format, confirmed_assumption_ids; job status/read/download permission checked live.

Main actions/output: Snapshot facts, rule trace, business versions, commercial lines, validity and caveats; structured report remains available if rendering fails.

Exceptions/recovery: Duplicate idempotency key reuses job; mismatched payload conflicts; failed export retries within limits; missing rate price=null; never a checkout.

This is an actor/system workflow, not a questionnaire; a fabricated question bank is not applicable. Related detailed admin/API/operations specs own resource fields and failure contracts. Every state change records actor, UTC time, reason, expected revision and target version.
