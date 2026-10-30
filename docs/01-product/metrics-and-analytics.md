# Metrics and analytics

Owner: R30. Reviewers: R01, R24. Status: proposed specification, 2026-10-29; no human approval.
Sources: [brief](../00-governance/sources/documentation-super-mega-prompt.md), [source inventory](../00-governance/source-inventory.md). Assumptions/open decisions: [DEC-01–10](../00-governance/assumptions-and-open-questions.md). Related IDs: NEED-01–08, NFR-12. Practice: [product-requirements](../00-governance/skills/product-requirements.md).

Metrics are proposed definitions; no baseline or achieved outcome exists.

| Metric | Exact definition/window | Guardrail |
|---|---|---|
| Completion | Runs obtaining reviewable result / consented eligible started runs, per flow/release/locale,7-day cohort | Separate voluntary abandonment, expiry and technical failures |
| Clarification burden | Median/p90 clarification screens per completed applicable path | Do not optimize by suppressing important unknowns |
| Useful next action | Post-result optional user response “I understand my next step” yes/no/unsure, denominator respondents | Response bias and nonresponse disclosed |
| Explainability | Moderated task: participant correctly identifies input and assumption behind material output | Research evidence pending |
| Estimate revision | Scope-driven vs price-driven result revisions per30-day cohort | Never interpret all revisions as failure |
| Technical success | Authorized report jobs completing within SLO / eligible jobs | Errors and retries categorized without answer content |
| Language parity | Completion/errors by locale with minimum cohort threshold | Suppress small cells, no individual profiling |

Event envelope: event_id, event_name, occurred_at_utc, pseudonymous run_id, flow_id, release_id, locale, surface, consent/purpose category, outcome_code, latency_ms. Proposed events: flow_started, answer_saved, clarification_shown, review_opened, result_requested/completed/failed, scenario_created, export_failed, draft_validated, release_published. Deduplicate by event_id; avoid raw answers, names, venture text, money values or tokens in analytics. R24 approves collection, retention and research consent. Opt-out analytics must not break essential saving/security records.

