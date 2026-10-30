# WF-SUPPORT procedure

Owner: R32. Reviewers: R05, R23, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-SUPPORT, AC-01–16. Open decisions: [register](../../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../../00-governance/skills/workflow-questionnaire.md).

Trigger/actors: User reports failed job/access/data request.

Preconditions/permissions: Verified requester, scoped purpose and authorized support/privacy role.

States: Requested -> triaged -> investigating -> awaiting_user/specialist -> resolved/rejected.

Form: request_id, run/job reference, problem_code, consent/purpose, minimal evidence; no passwords/tokens.

Main actions/output: Use sanitized diagnostics, guide owner edits, escalate security/price/legal questions. Export/delete requests track fulfillment and retained-policy exceptions.

Exceptions/recovery: Do not impersonate answers or bypass access. Failed exports retry safely; foreign project access denied; suspected breach escalates incident process.

This is an actor/system workflow, not a questionnaire; a fabricated question bank is not applicable. Related detailed admin/API/operations specs own resource fields and failure contracts. Every state change records actor, UTC time, reason, expected revision and target version.
