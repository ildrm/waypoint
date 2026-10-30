# WF-SETTINGS procedure

Owner: R18. Reviewers: R05, R23, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-SETTINGS, AC-01–16. Open decisions: [register](../../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../../00-governance/skills/workflow-questionnaire.md).

Trigger/actors: Shared default or flow override change.

Preconditions/permissions: Definition exists with type, range, allow_override and effect owner.

States: Draft -> type_validated -> impact_review -> approved -> published.

Form: PARAM ID, type, unit, default, allowed scopes, override value, reason, expected revision.

Main actions/output: Resolve definition -> shared release -> allowed flow release -> run snapshot; show value/source per affected flow. Live access policy never pinned as business setting.

Exceptions/recovery: Type mismatch, unknown parameter, override not allowed or circular reference fails. Preview all affected flows and rerun their reference scenarios.

This is an actor/system workflow, not a questionnaire; a fabricated question bank is not applicable. Related detailed admin/API/operations specs own resource fields and failure contracts. Every state change records actor, UTC time, reason, expected revision and target version.
