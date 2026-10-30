# Scenario comparison and sensitivity

Owner: R28. Reviewers: R29, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-SCENARIO, OUT-EFFORT, OUT-PRICE, OUT-SCHEDULE, OUT-TEAM. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [estimation-commercial](../00-governance/skills/estimation-commercial.md).

| Scenario | Scope/effort | Dependencies/duration | Team | Price basis/risk |
|---|---|---|---|---|
| Lean C01 |6 rather than 12 pages,2 rather than 3 content types; BA32, UX32, DEV120, QA48, OPS16=248 h | Discovery/design, build3wk, QA1.5wk, release;7wk planning,7–10 range |5 distinct, peak2, fractional as needed |B=13600, C1360, D748, N14212; content scope reduced |
| Balanced C01 |12 pages,3 types;40/40/160/64/16=320 h |8wk exact fixture,8–11 range |5 distinct, peak2 |B17600, C1760, D968, N18392 |
| Accelerated C01 |Same feature scope; add8 h BA coordination +8 h DEV integration,336 h |Two independent80 h developer tracks, then8 h integration; earliest7wk after rounding gates,7–9 range |6 distinct, peak3; no extra capacity assumed before start |B18560, C1856, D1020.80, N19395.20; coordination risk |

Accelerated336 h does not reuse the 6-week zero-coordination illustration as a commitment; extra integration/gates can push release to 7 weeks. All rates are PB-EXAMPLE-v1 and amounts exclude unconfigured taxes. Lean totals must be recalculated if deliverable quantities differ.

Top drivers: DEV hours+25%=+40 h =>B+2400 and net+2508 (×1.10 ×0.95), often+1 implementation week at 40 h; source content2-week delay can extend critical path2 weeks without extra engineering effort; extra required locale adds translation/review/export QA and content costs, not assumed product licensing. Compare recurrence and exclusions, not only build total. No scenario winner chosen by operator margin.
