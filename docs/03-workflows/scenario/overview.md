# WF-SCENARIO procedure

Owner: R28. Reviewers: R05, R23, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-SCENARIO, AC-01–16. Open decisions: [register](../../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../../00-governance/skills/workflow-questionnaire.md).

Trigger/actors: Owner selects baseline and changes explicit answers.

Preconditions/permissions: Baseline result accessible; no in-place rewrite.

States: Fork scenario -> edit -> clarify -> review -> calculate -> compare -> archive.

Form: baseline_result_id, changed_answer_ids, scenario_label, assumption acknowledgements; expected_revision and idempotency_key required.

Main actions/output: Show delta per surface/feature, WBS, price book, currency, duration, roles and recurring cost. Reused foundation once. A zero change produces equivalent structured result; changed currency cannot create unconverted numeric delta.

Exceptions/recovery: Stale baseline remains viewable; unsupported integration produces manual review. Repricing must show old/new catalog and revised validity.

This is an actor/system workflow, not a questionnaire; a fabricated question bank is not applicable. Related detailed admin/API/operations specs own resource fields and failure contracts. Every state change records actor, UTC time, reason, expected revision and target version.
