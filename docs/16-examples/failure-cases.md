# Complete failure/edge-case expectations

Owner: R27. Reviewers: R05, R23, R29. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: AC-01–16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [quality-documentation](../00-governance/skills/quality-documentation.md).

These are synthetic documentation fixtures and expected results, not executed application tests.

| ID | Case | Input/trigger | Exact expectation | Acceptance |
|---|---|---|---|---|
| F01 | Startup unknown | Q-ST-04 unknown | Evidence task, no zero/no score | AC-09,13 |
| F02 | Startup contradiction | 10=A,11=E same offering/period | C07 clarification; unresolved provisional | AC-09,13 |
| F03 | Once-only retention | 09=A, C01=C | 23 inactive, no churn penalty | AC-13 |
| F04 | Missing/invalid price | DEV rate absent or negative unapproved | Price null/incomplete; no invented total | AC-04 |
| F05 | No program match | All candidates violate hard constraint | No-match with reasons | AC-12 |
| F06 | Partial translations | Critical source hash changed infa | Stale, publication blocked; no silent fallback | AC-06,08 |
| F07 | Unsupported integration | API docs/sandbox absent | Feasibility discovery/manual-review; not claimed supported | AC-09 |
| F08 | Stale saved run | New release while run pinned old | Authorized resume old manifest or explicit compatibility recovery; never silent migration | AC-01,03 |
| F09 | Hidden paid mobile feature | Remove mobile after selected offline work | New app facts/charges invalidated; old report intact | AC-02,04 |
| F10 | Currency rounding | 100 lines×0.005USD with per-line billing unit | Each0.01, total 1.00; aggregate billing unit0.50 distinct catalog policy | AC-04 |
| F11 | Old price preservation | Bookv2 changes DEV rate | Old result retains bookv1; refresh new revision | AC-03,04 |
| F12 | Duplicate submission | Same key+same payload; then same key+different payload | Reuse operation; different payload409 | AC-15 |
| F13 | Access denial | Foreign run/export ID or revoked permission | Deny no data; recheck queued jobs/downloads | AC-07 |
| F14 | Failed export | Renderer unavailable | Structured report remains; bounded idempotent retry | AC-10 |
| F15 | Midflow locale switch | en→fa approved same release | Canonical facts preserved; RTL/formatting updated; critical unavailable explained | AC-06 |
| F16 | Expired refresh | Estimate validity passed, new book available | Explicit refresh new revision; history retained | AC-03,04 |
| F17 | Concurrent answer edits | Two writes expected_revision3 | First revision4; second412 no silent overwrite | AC-15 |
| F18 | Free completion | Complete all flows and export | No checkout, billing, payment or paid entitlement | AC-16 |

Every failure preserves authorized data and explicit state, records correlation/version references without sensitive payload, and routes to correction/retry/manual review. Do not turn unknowns into zero or payment demands.
