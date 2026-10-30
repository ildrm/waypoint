# Authoring and publication

Owner: R05. Reviewers: R18, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-AUTHOR, AC-08. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../00-governance/skills/workflow-questionnaire.md).

Author clones published flow into versioned draft; changes stable IDs or explicit deprecations, never reuses retired semantic IDs. Validate schema, question/option/rule references, dependency DAG, terminal routes, critical translation coverage, price references and report variables. Run boundary/unknown/back-edit fixtures and compare intended deltas. Reviewer approves exact draft hash. Publisher verifies authority and publishes atomic manifest with version dependencies in one transaction.

Scheduled activation locks approved payload; new edits cancel stale approval. Rollback selects earlier compatible release for new runs and records reason; never mutates old snapshots. Retirement stops new starts but supports authorized existing runs per compatibility/retention policy. Kill switch may block unsafe business output while preserving evidence and live access controls. Failure leaves prior release active; audit all attempts.
