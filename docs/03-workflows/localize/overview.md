# WF-LOCALIZE procedure

Owner: R16. Reviewers: R05, R23, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-LOCALIZE, AC-01–16. Open decisions: [register](../../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../../00-governance/skills/workflow-questionnaire.md).

Trigger/actors: Source hash changes or locale coverage missing.

Preconditions/permissions: Assigned translator/reviewer and approved terminology.

States: Draft -> in_review -> approved -> published; any source edit -> stale.

Form: locale, key, source_hash, translation, ICU placeholders, review note; admin UI locale separate.

Main actions/output: All 14 language entries required in production manifest. Critical copy exact source approval; same canonical answers preserve facts/prices.

Exceptions/recovery: Missing/stale critical text blocks locale/release. Translation may not alter rule meaning; semantic change requires source/rule review, not translator workaround.

This is an actor/system workflow, not a questionnaire; a fabricated question bank is not applicable. Related detailed admin/API/operations specs own resource fields and failure contracts. Every state change records actor, UTC time, reason, expected revision and target version.
