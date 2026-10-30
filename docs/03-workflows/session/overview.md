# WF-SESSION procedure

Owner: R23. Reviewers: R05, R23, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-SESSION, AC-01–16. Open decisions: [register](../../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../../00-governance/skills/workflow-questionnaire.md).

Trigger/actors: Start/save/resume/claim/expire.

Preconditions/permissions: Purpose notice; active release and current authorization.

States: Anonymous -> claimed/authenticated -> active -> paused -> completed/expired/deleted.

Form: flow_id, locale, project alias; secret resume token never in logs; authenticated claim requires proof and conflict check.

Main actions/output: Pin business config; save revisions atomically; token hash at rest; explicit provenance on cross-run reuse. User may edit only owned active run, not old immutable result.

Exceptions/recovery: Expired anonymous run uses recovery policy not guessable ID. Offline means unsaved until acknowledgement. Security revocation applies live; retention periods provisional.

This is an actor/system workflow, not a questionnaire; a fabricated question bank is not applicable. Related detailed admin/API/operations specs own resource fields and failure contracts. Every state change records actor, UTC time, reason, expected revision and target version.
