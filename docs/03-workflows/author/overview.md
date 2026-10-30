# WF-AUTHOR procedure

Owner: R05. Reviewers: R05, R23, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-AUTHOR, AC-01–16. Open decisions: [register](../../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../../00-governance/skills/workflow-questionnaire.md).

Trigger/actors: Authorized author clones or edits flow draft.

Preconditions/permissions: Staff role and draft version; referenced library release exists.

States: Draft -> validation_failed/validated -> in_review -> approved -> published -> retired.

Form: stable flow/stage/question/option IDs, schema, applicability, rule category/priority, parameters, translation keys, output template.

Main actions/output: Reviewer inspects exact diff and simulator evidence. Publisher cannot mutate a release; rollback selects previous compatible release for new runs.

Exceptions/recovery: Unreachable branch, undefined ID, conflicting rule, invalid price or missing critical translation blocks. Changed diff invalidates approval.

This is an actor/system workflow, not a questionnaire; a fabricated question bank is not applicable. Related detailed admin/API/operations specs own resource fields and failure contracts. Every state change records actor, UTC time, reason, expected revision and target version.
