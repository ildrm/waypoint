# Business invariants

Owner: R05. Reviewers: R17, R29, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: RULE-CORE-01–16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../00-governance/skills/workflow-questionnaire.md).

| ID | Invariant | Acceptance |
|---|---|---|
| RULE-CORE-01 | Stable IDs, never translated labels/order, drive business logic | AC-06 |
| RULE-CORE-02 | Unknown/no/zero/not-applicable/inactive/unanswered remain distinct | AC-09 |
| RULE-CORE-03 | Recompute applicability before facts/pricing; hidden selections never charge | AC-02 |
| RULE-CORE-04 | Business config pinned on session start; commercial book snapshot at calculation; security revocation live | AC-03,07 |
| RULE-CORE-05 | Typed finite restricted rules; no arbitrary admin code; conflicting material rules block publication | AC-08 |
| RULE-CORE-06 | Known startup blockers override optimistic aggregation; no success probability | AC-13 |
| RULE-CORE-07 | Eligibility first; no eligible service returns no-match, not fabricated tier | AC-12 |
| RULE-CORE-08 | Missing/unapproved/currency-mismatched rates produce incomplete price, not zero | AC-04 |
| RULE-CORE-09 | Exact decimal calculation and defined eligible adjustment bases; unique charge keys | AC-04 |
| RULE-CORE-10 | Dependencies, role capacity and review lead times determine schedule | AC-05 |
| RULE-CORE-11 | All 14 named languages required for production; critical-copy approvals invalidated by source edit | AC-06,08 |
| RULE-CORE-12 | Scope/repricing changes create visible immutable result revision | AC-03 |
| RULE-CORE-13 | Cross-project reuse requires explicit ownership/purpose/context/provenance confirmation | AC-01,07 |
| RULE-CORE-14 | Proficiency, target proficiency, project complexity and package tier are distinct | AC-12 |
| RULE-CORE-15 | Optional AI cannot alter authoritative structured facts or prices | AC-09 |
| RULE-CORE-16 | Platform is free; informational customer-project estimate is never a platform charge | AC-16 |

Flow-specific decision tables own branch/recommendation details; estimation contract owns monetary arithmetic. Resolve conflicts by category and explicit priority, never a global aggregate that hides a blocker.
